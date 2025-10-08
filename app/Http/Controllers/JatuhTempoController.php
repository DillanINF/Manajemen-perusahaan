<?php

namespace App\Http\Controllers;

use App\Models\JatuhTempo;
use App\Models\Invoice;
use App\Models\Customer;
use App\Models\PO;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Mail;

class JatuhTempoController extends Controller
{
    public function index()
    {
        // Filter bulan/tahun dan status pembayaran
        $bulan = (int) request()->get('month', (int) Carbon::now()->format('n'));
        $tahun = (int) request()->get('year', (int) Carbon::now()->format('Y'));
        $statusPembayaran = request()->get('status'); // null|Belum Bayar|Lunas

        $query = JatuhTempo::query()
            ->leftJoin('invoices', function($join) {
                // Kaitan utama via No PO agar mengambil No Invoice dari Data Invoice
                $join->on('invoices.no_po', '=', 'jatuh_tempos.no_po');
            })
            // Ambil semua kolom JT + override no_invoice dengan yang dari Data Invoice bila tersedia
            ->select('jatuh_tempos.*', \DB::raw("COALESCE(NULLIF(invoices.no_invoice, ''), jatuh_tempos.no_invoice) as no_invoice"));
        $query->whereYear('jatuh_tempos.tanggal_invoice', $tahun)
              ->whereMonth('jatuh_tempos.tanggal_invoice', $bulan);
        if ($statusPembayaran) {
            $query->where('status_pembayaran', $statusPembayaran);
        }

        $jatuhTempos = $query->orderBy('jatuh_tempos.tanggal_jatuh_tempo', 'asc')->get();

        // Monthly stats like Surat Jalan (for the selected year)
        $statsBuilder = JatuhTempo::query()->whereYear('tanggal_invoice', $tahun);
        if ($statusPembayaran) {
            $statsBuilder->where('status_pembayaran', $statusPembayaran);
        }
        // Initialize empty array for all months
        $monthlyStats = [];
        
        // Get stats for each month
        for ($i = 1; $i <= 12; $i++) {
            $monthQuery = clone $statsBuilder;
            $monthData = $monthQuery
                ->whereMonth('tanggal_invoice', $i)
                ->selectRaw('COUNT(*) as total_count, COALESCE(SUM(jumlah_tagihan), 0) as total_sum')
                ->first();
                
            $monthlyStats[$i] = (object) [
                'total_count' => (int) ($monthData->total_count ?? 0),
                'total_sum' => (float) ($monthData->total_sum ?? 0),
            ];
        }

        // Statistik sesuai kebutuhan user
        // Total Tagihan Pending (hanya yang status Belum Bayar/Sebagian)
        $totalTagihanPending = $jatuhTempos->where('status_pembayaran', '!=', 'Lunas')->sum('jumlah_tagihan');
        
        // Total Terbayar (hanya yang status Lunas/Accept)
        $totalTerbayar = $jatuhTempos->where('status_pembayaran', 'Lunas')->sum('jumlah_tagihan');
        
        // Total Customer Pending (unique customer dengan status pending)
        $totalCustomerPending = $jatuhTempos->where('status_pembayaran', '!=', 'Lunas')->unique('customer')->count();
        
        // Total Customer Accept (unique customer dengan status accept)
        $totalCustomerAccept = $jatuhTempos->where('status_pembayaran', 'Lunas')->unique('customer')->count();

        // Get customers for dropdown
        $customers = Customer::orderBy('name')->get();

        return view('jatuh-tempo.index', compact(
            'jatuhTempos', 'totalTagihanPending', 'totalTerbayar', 'totalCustomerPending', 'totalCustomerAccept',
            'bulan', 'tahun', 'statusPembayaran', 'monthlyStats', 'customers'
        ));
    }

    public function create()
    {
        $invoices = Invoice::all();
        return view('jatuh-tempo.create', compact('invoices'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'no_invoice' => 'required|string|unique:jatuh_tempos',
            'no_po' => 'required|string',
            'customer' => 'required|string',
            'tanggal_invoice' => 'required|date',
            'tanggal_jatuh_tempo' => 'nullable|date',
            'jumlah_tagihan' => 'required|integer|min:0',
            'jumlah_terbayar' => 'nullable|integer|min:0',
            // Accept both UI terms and DB enums
            'status_pembayaran' => 'required|in:Belum Bayar,Sebagian,Lunas,Pending,Accept',
            'status_approval' => 'nullable|in:Pending,ACC,Reject',
            'denda' => 'nullable|integer|min:0',
            'catatan' => 'nullable|string'
        ]);

        // Map UI values to DB enum values
        if (($data['status_pembayaran'] ?? null) === 'Pending') {
            $data['status_pembayaran'] = 'Belum Bayar';
        } elseif (($data['status_pembayaran'] ?? null) === 'Accept') {
            $data['status_pembayaran'] = 'Lunas';
        }

        // Hitung sisa tagihan
        $data['jumlah_terbayar'] = $data['jumlah_terbayar'] ?? 0;
        $data['sisa_tagihan'] = $data['jumlah_tagihan'] - $data['jumlah_terbayar'];

        // Default status approval = Pending jika tidak diisi
        $data['status_approval'] = $data['status_approval'] ?? 'Pending';

        // Jika tanggal jatuh tempo kosong, set +1 bulan dari tanggal invoice
        if (empty($data['tanggal_jatuh_tempo']) && !empty($data['tanggal_invoice'])) {
            // Coba ambil payment terms dari Customer berdasarkan nama
            $termsDays = null;
            if (!empty($data['customer'])) {
                $cust = Customer::where('name', $data['customer'])->first();
                if ($cust && !empty($cust->payment_terms_days)) {
                    $termsDays = (int) $cust->payment_terms_days;
                }
            }

            if ($termsDays && $termsDays > 0) {
                $data['tanggal_jatuh_tempo'] = Carbon::parse($data['tanggal_invoice'])->addDays($termsDays)->format('Y-m-d');
            } else {
                // Fallback jika tidak ada terms: +1 bulan
                $data['tanggal_jatuh_tempo'] = Carbon::parse($data['tanggal_invoice'])->addMonth()->format('Y-m-d');
            }
        }

        JatuhTempo::create($data);

        // Simpan parameter filter untuk redirect
        $params = [];
        if (request()->get('month')) $params['month'] = request()->get('month');
        if (request()->get('year')) $params['year'] = request()->get('year');
        if (request()->get('status')) $params['status'] = request()->get('status');
        
        return redirect()->route('jatuh-tempo.index', $params)->with('success', 'Data Jatuh Tempo berhasil dibuat.');
    }

    public function edit($id)
    {
        try {
            $jatuhTempo = JatuhTempo::findOrFail($id);
            
            // If it's an AJAX request, return JSON
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'id' => $jatuhTempo->id,
                        'no_invoice' => $jatuhTempo->no_invoice,
                        'no_po' => $jatuhTempo->no_po,
                        'customer' => $jatuhTempo->customer,
                        'tanggal_invoice' => $jatuhTempo->tanggal_invoice->format('Y-m-d'),
                        'tanggal_jatuh_tempo' => $jatuhTempo->tanggal_jatuh_tempo ? Carbon::parse($jatuhTempo->tanggal_jatuh_tempo)->format('Y-m-d') : null,
                        'jumlah_tagihan' => $jatuhTempo->jumlah_tagihan,
                        'jumlah_terbayar' => $jatuhTempo->jumlah_terbayar ?? 0,
                        'denda' => $jatuhTempo->denda ?? 0,
                        'status_pembayaran' => $jatuhTempo->status_pembayaran === 'Lunas' ? 'Accept' : 'Pending',
                        'status_approval' => $jatuhTempo->status_approval ?? 'Pending',
                        'catatan' => $jatuhTempo->catatan
                    ]
                ]);
            }
            
            // For regular requests, return view (existing functionality)
            $jatuhTempos = JatuhTempo::orderBy('tanggal_jatuh_tempo', 'asc')->get();
            $invoices = Invoice::all();

            // Statistik
            $totalTagihan = $jatuhTempos->sum('jumlah_tagihan');
            $totalTerbayar = $jatuhTempos->sum('jumlah_terbayar');
            $totalOverdue = $jatuhTempos->where('is_overdue', true)->sum('sisa_tagihan');
            $countOverdue = $jatuhTempos->where('is_overdue', true)->count();

            return view('jatuh-tempo.index', compact('jatuhTempo', 'jatuhTempos', 'invoices', 'totalTagihan', 'totalTerbayar', 'totalOverdue', 'countOverdue'));
        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }
            
            return redirect()->route('jatuh-tempo.index')->with('error', 'Data tidak ditemukan');
        }
    }

    public function update(Request $request, JatuhTempo $jatuhTempo)
    {
        $data = $request->validate([
            'no_invoice' => 'required|string|unique:jatuh_tempos,no_invoice,' . $jatuhTempo->id,
            'no_po' => 'required|string',
            'customer' => 'required|string',
            'tanggal_invoice' => 'required|date',
            'tanggal_jatuh_tempo' => 'nullable|date',
            'jumlah_tagihan' => 'required|integer|min:0',
            'jumlah_terbayar' => 'nullable|integer|min:0',
            // Accept both UI terms and DB enums
            'status_pembayaran' => 'required|in:Belum Bayar,Sebagian,Lunas,Pending,Accept',
            'status_approval' => 'nullable|in:Pending,ACC,Reject',
            'denda' => 'nullable|integer|min:0',
            'catatan' => 'nullable|string'
        ]);

        // Map UI values to DB enum values
        if (($data['status_pembayaran'] ?? null) === 'Pending') {
            $data['status_pembayaran'] = 'Belum Bayar';
        } elseif (($data['status_pembayaran'] ?? null) === 'Accept') {
            $data['status_pembayaran'] = 'Lunas';
        }

        // Hitung sisa tagihan
        $data['jumlah_terbayar'] = $data['jumlah_terbayar'] ?? 0;
        $data['sisa_tagihan'] = $data['jumlah_tagihan'] - $data['jumlah_terbayar'];

        // Pertahankan atau default status approval
        $data['status_approval'] = $data['status_approval'] ?? $jatuhTempo->status_approval ?? 'Pending';

        // Jika tanggal jatuh tempo kosong, hitung dari payment terms customer; fallback +1 bulan
        if (empty($data['tanggal_jatuh_tempo']) && !empty($data['tanggal_invoice'])) {
            $termsDays = null;
            if (!empty($data['customer'])) {
                $cust = Customer::where('name', $data['customer'])->first();
                if ($cust && !empty($cust->payment_terms_days)) {
                    $termsDays = (int) $cust->payment_terms_days;
                }
            }

            if ($termsDays && $termsDays > 0) {
                $data['tanggal_jatuh_tempo'] = Carbon::parse($data['tanggal_invoice'])->addDays($termsDays)->format('Y-m-d');
            } else {
                $data['tanggal_jatuh_tempo'] = Carbon::parse($data['tanggal_invoice'])->addMonth()->format('Y-m-d');
            }
        }

        $jatuhTempo->update($data);

        // Simpan parameter filter untuk redirect
        $params = [];
        if (request()->get('month')) $params['month'] = request()->get('month');
        if (request()->get('year')) $params['year'] = request()->get('year');
        if (request()->get('status')) $params['status'] = request()->get('status');
        return redirect()->route('jatuh-tempo.index', $params)->with('success', 'Data Jatuh Tempo berhasil diperbarui.');
    }

    public function destroy(JatuhTempo $jatuhTempo)
    {
        // Utamakan bulan/tahun dari request (bulan yang sedang dilihat user)
        $reqMonth = request()->get('month');
        $reqYear = request()->get('year');
        $statusApproval = request()->get('status');

        // Fallback ke bulan/tahun dari data jika request tidak menyertakan
        $itemMonth = $jatuhTempo->tanggal_jatuh_tempo ? date('n', strtotime($jatuhTempo->tanggal_jatuh_tempo)) : null;
        $itemYear = $jatuhTempo->tanggal_jatuh_tempo ? date('Y', strtotime($jatuhTempo->tanggal_jatuh_tempo)) : null;

        $jatuhTempo->delete();
        
        // Redirect dengan parameter bulan, tahun, dan status yang sama
        $params = [];
        if ($reqMonth ?? false) {
            $params['month'] = $reqMonth;
        } elseif ($itemMonth) {
            $params['month'] = $itemMonth;
        }
        if ($reqYear ?? false) {
            $params['year'] = $reqYear;
        } elseif ($itemYear) {
            $params['year'] = $itemYear;
        }
        if ($statusApproval) $params['status'] = $statusApproval;
        
        return redirect()->route('jatuh-tempo.index', $params)->with('success', 'Data Jatuh Tempo berhasil dihapus.');
    }

    public function sendReminder($id)
    {
        $jatuhTempo = JatuhTempo::findOrFail($id);
        
        // Update reminder status
        $jatuhTempo->update([
            'reminder_sent' => true,
            'last_reminder_date' => Carbon::now()
        ]);

        return redirect()->route('jatuh-tempo.index')->with('success', 'Reminder berhasil dikirim.');
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                // UI terms (Accept/Pending) maupun enum DB (Belum Bayar/Sebagian/Lunas)
                'status_pembayaran' => 'required|in:Belum Bayar,Sebagian,Lunas,Accept,Pending',
            ]);

            $jatuhTempo = JatuhTempo::findOrFail($id);

            // Map UI => DB
            $incoming = $validated['status_pembayaran'];
            $dbStatus = $incoming;
            if ($incoming === 'Pending') { $dbStatus = 'Belum Bayar'; }
            if ($incoming === 'Accept') { $dbStatus = 'Lunas'; }

            $jatuhTempo->update([
                'status_pembayaran' => $dbStatus,
            ]);

            // Map DB => UI untuk balasan
            $uiStatus = $dbStatus === 'Lunas' ? 'Accept' : 'Pending';

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui',
                'new_status' => $uiStatus,
                'db_status' => $dbStatus,
            ]);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $ve->errors(),
            ], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $mnf) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan',
            ], 404);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate status: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function updateDeadline(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'tanggal_jatuh_tempo' => 'required|date',
            ]);

            $jatuhTempo = JatuhTempo::findOrFail($id);
            $jatuhTempo->update([
                'tanggal_jatuh_tempo' => $validated['tanggal_jatuh_tempo'],
            ]);

            // Simpan parameter filter untuk redirect
            $params = [];
            if (request()->get('month')) $params['month'] = request()->get('month');
            if (request()->get('year')) $params['year'] = request()->get('year');
            if (request()->get('status')) $params['status'] = request()->get('status');
            
            return redirect()->route('jatuh-tempo.index', $params)->with('success', 'Deadline berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengupdate deadline: ' . $e->getMessage());
        }
    }

    /**
     * Kirim email notifikasi untuk jatuh tempo yang sudah lewat
     */
    public function notify(Request $request, $id)
    {
        try {
            $jt = JatuhTempo::findOrFail($id);

            // Hanya boleh kirim jika sudah jatuh tempo dan belum Lunas
            $today = Carbon::today();
            $deadline = Carbon::parse($jt->tanggal_jatuh_tempo);
            if (!($jt->status_pembayaran !== 'Lunas' && $deadline->lte($today))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hanya bisa mengirim email untuk tagihan yang sudah jatuh tempo dan belum Lunas.'
                ], 400);
            }

            // Ambil email customer berdasarkan nama
            $customer = Customer::where('name', $jt->customer)->orWhere('nama_customer', $jt->customer)->first();
            if (!$customer || empty($customer->email)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email customer tidak ditemukan. Lengkapi email pada Data Customer.'
                ], 400);
            }

            // Ambil pesan custom dari request
            $customMessage = $request->input('custom_message', '');

            // Kirim email dengan template yang lebih menarik
            $subject = '🔔 Pemberitahuan Jatuh Tempo - ' . ($jt->no_invoice ?: 'Tanpa No Invoice');
            
            // Hitung hari terlambat
            $daysOverdue = $today->diffInDays($deadline);
            $overdueText = $daysOverdue > 0 ? "Terlambat {$daysOverdue} hari" : "Jatuh tempo hari ini";
            
            $body = "Kepada Yth. {$customer->name},\n\n" .
                    "Kami ingin menginformasikan bahwa tagihan berikut telah jatuh tempo:\n\n" .
                    "📋 DETAIL TAGIHAN:\n" .
                    "• No Invoice: " . ($jt->no_invoice ?: '-') . "\n" .
                    "• No PO/Tanggal SJ: " . ($jt->no_po ?: '-') . "\n" .
                    "• Tanggal Jatuh Tempo: " . $deadline->format('d F Y') . "\n" .
                    "• Status: {$overdueText}\n" .
                    "• Jumlah Tagihan: Rp " . number_format((float)$jt->jumlah_tagihan, 0, ',', '.') . "\n\n";

            // Tambahkan pesan custom jika ada
            if (!empty($customMessage)) {
                $body .= "💬 PESAN KHUSUS:\n" . $customMessage . "\n\n";
            }

            $body .= "Mohon segera melakukan pembayaran untuk menghindari keterlambatan lebih lanjut.\n\n" .
                     "Untuk konfirmasi pembayaran atau informasi lebih lanjut, silakan hubungi kami.\n\n" .
                     "Terima kasih atas perhatian dan kerjasamanya.\n\n" .
                     "Hormat kami,\n" .
                     "PT. CAM JAYA ABADI\n" .
                     "📧 Email ini dikirim otomatis oleh sistem";

            Mail::raw($body, function ($message) use ($customer, $subject) {
                $message->to($customer->email)
                        ->subject($subject)
                        ->from(config('mail.from.address'), 'PT. CAM JAYA ABADI');
            });

            return response()->json([
                'success' => true,
                'message' => "📧 Email berhasil dikirim ke {$customer->email}",
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim email: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Kirim email detail invoice dengan daftar barang ke customer
     */
    public function sendInvoiceDetail(Request $request, $id)
    {
        try {
            $jatuhTempo = JatuhTempo::findOrFail($id);
            
            // Resolusi email customer yang kuat:
            // 1) Cari PO berdasarkan no_po untuk mendapatkan customer_id
            // 2) Fallback: cari berdasarkan nama jika customer_id tidak ditemukan
            $customer = null;
            if (!empty($jatuhTempo->no_po)) {
                $poHeader = PO::where('no_po', $jatuhTempo->no_po)->orderByDesc('id')->first();
                if ($poHeader && $poHeader->customer_id) {
                    $customer = Customer::find($poHeader->customer_id);
                }
            }
            if (!$customer) {
                $customer = Customer::where('name', $jatuhTempo->customer)
                    ->orWhere('nama_customer', $jatuhTempo->customer)
                    ->first();
            }

            if (!$customer || empty($customer->email)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email customer tidak ditemukan. Lengkapi email pada Data Customer.',
                ], 400);
            }

            // Ambil data barang dari PO berdasarkan no_po
            $items = \App\Models\PO::where('no_po', $jatuhTempo->no_po)
                ->with('produkRel')
                ->get();

            // Debug: Log jumlah items
            \Log::info('Items count for no_po ' . $jatuhTempo->no_po . ': ' . $items->count());
            if ($items->count() > 0) {
                foreach ($items as $item) {
                    \Log::info('Item: ' . ($item->produkRel->nama_produk ?? 'No product') . ' - Qty: ' . $item->qty . ' ' . $item->qty_jenis);
                }
            }

            // Ambil data admin yang sedang login
            $admin = auth()->user();
            $adminEmail = $admin->email;
            $adminName = $admin->name;

            // Kirim email dengan template detail invoice
            Mail::to($customer->email)->send(
                new \App\Mail\InvoiceDetailMail($jatuhTempo, $items, $adminEmail, $adminName)
            );

            return response()->json([
                'success' => true,
                'message' => "📧 Email detail invoice berhasil dikirim ke {$customer->email}",
            ]);
        } catch (\Throwable $e) {
            \Log::error('Send invoice detail email error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim email: ' . $e->getMessage(),
            ], 500);
        }
    }

    // Method destroyAll() dihapus sesuai permintaan untuk mencegah penghapusan massal
}
