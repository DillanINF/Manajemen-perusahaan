<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SuratJalan;
use App\Models\PO;
use App\Models\Produk;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use App\Exports\SuratJalanExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use App\Models\JatuhTempo;
use App\Services\DatabaseService;
use App\Models\SisaPOItem;

class SuratJalanController extends Controller
{
    /**
     * Menampilkan daftar Surat Jalan.
     */
    public function index(Request $request)
    {
        // Ambil filter bulan/tahun dari query string
        $month = (int) ($request->get('month') ?: now()->format('n'));
        $year  = (int) ($request->get('year')  ?: now()->format('Y'));
        
        // PERBAIKAN: Ambil filter po_number untuk menampilkan data spesifik per invoice
        $poNumber = $request->get('po_number');

        // Daftar tahun dari database
        $dbYears = collect();
        $years = DB::table('pos')
            ->select('tanggal_po')
            ->distinct()
            ->get()
            ->map(function($item) {
                return (int) date('Y', strtotime($item->tanggal_po));
            });
            
        $dbYears = $years->unique()->sort()->values();

        // Rentang tahun 2025-2030 sebagai default
        $currentYear = (int) Carbon::now()->format('Y');
        $defaultYears = range(2025, 2030);
        
        // Tambahkan tahun sekarang jika tidak dalam range 2025-2030
        if ($currentYear < 2025 || $currentYear > 2030) {
            $defaultYears[] = $currentYear;
            sort($defaultYears);
        }

        $availableYears = collect($defaultYears);
        
        // Daftar semua tahun untuk modal (dari 2020 sampai 2035)
        $allYears = range(2020, 2035);

        // PERBAIKAN: Filter data berdasarkan po_number jika ada
        $suratjalan = PO::with(['produkRel', 'kendaraanRel'])
            ->when($year, fn($q) => $q->whereRaw(DatabaseService::year('tanggal_po') . ' = ?', [$year]))
            ->when($month, fn($q) => $q->whereRaw(DatabaseService::month('tanggal_po') . ' = ?', [$month]))
            ->when($poNumber, fn($q) => $q->where('po_number', (int)$poNumber))
            // Hanya tampilkan data PO yang benar-benar diinput (bukan placeholder dari Data Invoice)
            ->whereNotNull('no_po')
            ->where('no_po', '!=', '-')
            ->orderBy('created_at', 'desc')
            ->get();

        // Total agregat untuk tampilan (jumlah transaksi & total rupiah)
        $totalTransaksi = $suratjalan->count();
        $totalRupiah = (int) $suratjalan->sum(function ($r) {
            return (int) ($r->total ?? 0);
        });

        // Ambil semua produk untuk dropdown
        $produk = Produk::all();

        // PERBAIKAN: Filter data PO berdasarkan po_number jika ada
        $pos = PO::with(['produkRel', 'kendaraanRel'])
            ->when($year, fn($q) => $q->whereRaw(DatabaseService::year('tanggal_po') . ' = ?', [$year]))
            ->when($month, fn($q) => $q->whereRaw(DatabaseService::month('tanggal_po') . ' = ?', [$month]))
            ->when($poNumber, fn($q) => $q->where('po_number', (int)$poNumber))
            ->whereNotNull('no_po')
            ->where('no_po', '!=', '-')
            ->orderBy('created_at', 'desc')
            ->get();

        // CHANGE: Tambahkan data pengirim untuk dropdown
        $pengirims = DB::table('pos')
            ->select('pengirim as nama')
            ->whereNotNull('pengirim')
            ->where('pengirim', '!=', '')
            ->distinct()
            ->orderBy('pengirim', 'asc')
            ->get();

        // Ringkasan total PO per bulan pada tahun terpilih
        $monthlyStats = collect();
        
        for ($m = 1; $m <= 12; $m++) {
            $monthData = DB::table('pos')
                ->select(DB::raw('SUM(total) as total_sum, COUNT(*) as total_count'))
                ->when($poNumber, fn($q) => $q->where('po_number', (int) $poNumber))
                ->whereNotNull('no_po')
                ->where('no_po', '!=', '-')
                ->whereYear('tanggal_po', $year)
                ->whereMonth('tanggal_po', $m)
                ->first();
                
            if ($monthData) {
                $monthlyStats->put($m, (object)[
                    'month' => $m,
                    'total_sum' => $monthData->total_sum ?? 0,
                    'total_count' => $monthData->total_count ?? 0,
                ]);
            }
        }

        return view('suratjalan.index', [
            'suratjalan'      => $suratjalan,
            'produk'          => $produk,
            'pos'             => $pos,
            'pengirims'       => $pengirims,
            'availableYears'  => $availableYears,
            'allYears'        => $allYears,
            'month'           => $month,
            'year'            => $year,
            'poNumber'        => $poNumber,
            'totalTransaksi'  => $totalTransaksi,
            'totalRupiah'     => $totalRupiah,
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'tanggal_po'    => 'required|date',
            'customer'      => 'required|string',
            'no_surat_jalan'=> 'required|string',
            'no_po'         => 'required|string',
            'kendaraan'     => 'nullable|string',
            'no_polisi'     => 'required|string',
            'qty'           => 'required|integer|min:1',
            'qty_jenis'     => 'required|string',
            'produk_id'     => 'required|exists:produks,id',
            'total'         => 'required|numeric',
            'alamat_1'      => 'required|string|max:500',
            'alamat_2'      => 'nullable|string|max:500',
            'pengirim'      => 'nullable|string|max:255',
        ]);

        // Validasi stok tidak boleh 0
        $produk = Produk::with(['barangMasuks', 'barangKeluars'])->find($request->produk_id);
        if ($produk) {
            $stokMasuk = $produk->barangMasuks->sum('qty');
            $stokKeluar = $produk->barangKeluars->sum('qty');
            $stokTersedia = $stokMasuk - $stokKeluar;
            
            if ($stokTersedia <= 0) {
                return redirect()->back()
                    ->withErrors(['produk_id' => 'Produk "' . $produk->nama_produk . '" memiliki stok 0. Tidak dapat diinput ke Surat Jalan.'])
                    ->withInput();
            }
            
            if ($request->qty > $stokTersedia) {
                return redirect()->back()
                    ->withErrors(['qty' => 'Qty yang diminta (' . $request->qty . ') melebihi stok tersedia (' . $stokTersedia . ') untuk produk "' . $produk->nama_produk . '".'])
                    ->withInput();
            }
        }

        $data = $request->only([
            'tanggal_po', 'customer', 'no_surat_jalan', 'no_po', 'kendaraan',
            'no_polisi', 'qty', 'qty_jenis', 'produk_id', 'total', 'alamat_1', 'alamat_2',
            'pengirim'
        ]);
        // Kompatibilitas schema lama: isi kolom legacy 'po_number' jika ada constraint NOT NULL
        $data['po_number'] = $data['no_po'] ?? null;

        // Kompatibilitas schema lama: isi kolom legacy 'po_number' jika ada constraint NOT NULL
        $data['po_number'] = $data['no_po'] ?? null;

        // PERBAIKAN: Bersihkan data kendaraan yang invalid
        if (isset($data['kendaraan'])) {
            if ($data['kendaraan'] === '0' || $data['kendaraan'] === '' || $data['kendaraan'] === 'tes' || empty($data['kendaraan'])) {
                $data['kendaraan'] = null;
            } elseif (is_numeric($data['kendaraan']) && (int)$data['kendaraan'] === 0) {
                $data['kendaraan'] = null;
            }
        }

        if (empty($data['alamat_1']) || $data['alamat_1'] === '-' || trim($data['alamat_1']) === '') {
            return redirect()->back()
                ->withErrors(['alamat_1' => 'Alamat 1 harus diisi dan tidak boleh kosong'])
                ->withInput();
        }

        $po = PO::create($data);

        // Sinkronisasi ke Jatuh Tempo: gunakan payment_terms_days bila ada, fallback +1 bulan
        try {
            $invoiceKey = $po->no_invoice ?: $po->no_surat_jalan; // pakai invoice jika ada, jika tidak pakai no_surat_jalan
            $tanggalInvoice = Carbon::parse($po->tanggal_po);
            // Cari terms customer berdasarkan nama pada PO
            $customer = Customer::where('name', $po->customer)->first();
            $termsDays = (int) (($customer->payment_terms_days ?? 0));
            if ($termsDays > 0) {
                $tanggalJatuhTempo = (clone $tanggalInvoice)->addDays($termsDays);
            } else {
                $tanggalJatuhTempo = (clone $tanggalInvoice)->addMonth();
            }

            JatuhTempo::updateOrCreate(
                ['no_invoice' => $invoiceKey],
                [
                    'no_po' => $po->no_po,
                    'customer' => $po->customer,
                    'tanggal_invoice' => $tanggalInvoice->format('Y-m-d'),
                    'tanggal_jatuh_tempo' => $tanggalJatuhTempo->format('Y-m-d'),
                    'jumlah_tagihan' => (int) ($po->total ?? 0),
                    'jumlah_terbayar' => 0,
                    'sisa_tagihan' => (int) ($po->total ?? 0),
                    'status_pembayaran' => 'Belum Bayar',
                    'status_approval' => 'Pending',
                ]
            );
        } catch (\Throwable $e) {
            \Log::warning('SJ Sync JatuhTempo gagal: ' . $e->getMessage());
        }

        return redirect()->route('suratjalan.index')
            ->with('success', 'Surat jalan berhasil ditambahkan');
    }

    /**
     * Mengupdate Surat Jalan.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal_po'    => 'required|date',
            'customer'      => 'required|string',
            'no_surat_jalan'=> 'required|string',
            'no_po'         => 'required|string',
            'kendaraan'     => 'nullable|string',
            'no_polisi'     => 'required|string',
            'qty'           => 'required|integer|min:1',
            'qty_jenis'     => 'required|string',
            'produk_id'     => 'required|exists:produks,id',
            'total'         => 'required|numeric',
            'alamat_1'      => 'required|string|max:500',
            'alamat_2'      => 'nullable|string|max:500',
            'pengirim'      => 'nullable|string|max:255',
        ]);

        // Validasi stok tidak boleh 0
        $produk = Produk::with(['barangMasuks', 'barangKeluars'])->find($request->produk_id);
        if ($produk) {
            $stokMasuk = $produk->barangMasuks->sum('qty');
            $stokKeluar = $produk->barangKeluars->sum('qty');
            $stokTersedia = $stokMasuk - $stokKeluar;
            
            if ($stokTersedia <= 0) {
                return redirect()->back()
                    ->withErrors(['produk_id' => 'Produk "' . $produk->nama_produk . '" memiliki stok 0. Tidak dapat diupdate di Surat Jalan.'])
                    ->withInput();
            }
            
            if ($request->qty > $stokTersedia) {
                return redirect()->back()
                    ->withErrors(['qty' => 'Qty yang diminta (' . $request->qty . ') melebihi stok tersedia (' . $stokTersedia . ') untuk produk "' . $produk->nama_produk . '".'])
                    ->withInput();
            }
        }

        $suratJalan = PO::findOrFail($id);
        
        $data = $request->only([
            'tanggal_po', 'customer', 'no_surat_jalan', 'no_po', 'kendaraan',
            'no_polisi', 'qty', 'qty_jenis', 'produk_id', 'total', 'alamat_1', 'alamat_2',
            'pengirim'
        ]);

        // PERBAIKAN: Bersihkan data kendaraan yang invalid
        if (isset($data['kendaraan'])) {
            if ($data['kendaraan'] === '0' || $data['kendaraan'] === '' || $data['kendaraan'] === 'tes' || empty($data['kendaraan'])) {
                $data['kendaraan'] = null;
            } elseif (is_numeric($data['kendaraan']) && (int)$data['kendaraan'] === 0) {
                $data['kendaraan'] = null;
            }
        }

        if (empty($data['alamat_1']) || $data['alamat_1'] === '-' || trim($data['alamat_1']) === '') {
            return redirect()->back()
                ->withErrors(['alamat_1' => 'Alamat 1 harus diisi dan tidak boleh kosong'])
                ->withInput();
        }

        $suratJalan->update($data);

        // Sinkronisasi ke Jatuh Tempo (update): gunakan payment_terms_days dan pertahankan jumlah_terbayar jika ada
        try {
            $invoiceKey = $suratJalan->no_invoice ?: $suratJalan->no_surat_jalan;
            $tanggalInvoice = Carbon::parse($suratJalan->tanggal_po);
            // Cari terms customer berdasarkan nama pada PO
            $customer = Customer::where('name', $suratJalan->customer)->first();
            $termsDays = (int) (($customer->payment_terms_days ?? 0));
            if ($termsDays > 0) {
                $tanggalJatuhTempo = (clone $tanggalInvoice)->addDays($termsDays);
            } else {
                $tanggalJatuhTempo = (clone $tanggalInvoice)->addMonth();
            }

            $existingJT = JatuhTempo::where('no_invoice', $invoiceKey)->first();
            $jumlahTerbayar = $existingJT ? (int) ($existingJT->jumlah_terbayar ?? 0) : 0;
            $jumlahTagihan = (int) ($suratJalan->total ?? 0);

            JatuhTempo::updateOrCreate(
                ['no_invoice' => $invoiceKey],
                [
                    'no_po' => $suratJalan->no_po,
                    'customer' => $suratJalan->customer,
                    'tanggal_invoice' => $tanggalInvoice->format('Y-m-d'),
                    'tanggal_jatuh_tempo' => $tanggalJatuhTempo->format('Y-m-d'),
                    'jumlah_tagihan' => $jumlahTagihan,
                    'jumlah_terbayar' => $jumlahTerbayar,
                    'sisa_tagihan' => max(0, $jumlahTagihan - $jumlahTerbayar),
                    'status_pembayaran' => $jumlahTerbayar >= $jumlahTagihan ? 'Lunas' : ($jumlahTerbayar > 0 ? 'Sebagian' : 'Belum Bayar'),
                    'status_approval' => $existingJT->status_approval ?? 'Pending',
                ]
            );
        } catch (\Throwable $e) {
            \Log::warning('SJ Sync JatuhTempo (update) gagal: ' . $e->getMessage());
        }

        return redirect()->route('suratjalan.index')
            ->with('success', 'Surat jalan berhasil diupdate');
    }

    /**
     * Menghapus Surat Jalan.
     */
    public function destroy($id)
    {
        try {
            $suratJalan = PO::findOrFail($id);
            
            // Simpan bulan dan tahun dari data yang akan dihapus
            $month = $suratJalan->tanggal_po ? date('n', strtotime($suratJalan->tanggal_po)) : null;
            $year = $suratJalan->tanggal_po ? date('Y', strtotime($suratJalan->tanggal_po)) : null;

            // JANGAN hapus Jatuh Tempo berdasarkan no_invoice saat menghapus Data PO
            // Agar invoice tidak ikut hilang dari daftar invoice.
            // Hanya bersihkan JatuhTempo yang terikat spesifik ke no_po ini jika ada.
            try {
                if (!empty($suratJalan->no_po)) {
                    JatuhTempo::where('no_po', $suratJalan->no_po)->delete();
                }
            } catch (\Throwable $e) {
                \Log::warning('[JT] Gagal membersihkan JatuhTempo by no_po saat hapus Surat Jalan', [
                    'error' => $e->getMessage(),
                    'no_po' => $suratJalan->no_po,
                ]);
            }
            
            // Rollback stok: hapus catatan BarangKeluar yang dibuat otomatis oleh PO ini
            try {
                $noPoTrim = trim((string) $suratJalan->no_po);
                if ($noPoTrim !== '') {
                    $items = $suratJalan->items()->get(['produk_id']);
                    foreach ($items as $it) {
                        \App\Models\BarangKeluar::where('produk_id', $it->produk_id)
                            ->where(function($q) use ($suratJalan, $noPoTrim) {
                                $exact = 'Auto Keluar dari PO ' . (string) $suratJalan->no_po;
                                $exactTrim = 'Auto Keluar dari PO ' . $noPoTrim;
                                $q->where('keterangan', $exact)
                                  ->orWhere('keterangan', $exactTrim)
                                  ->orWhere('keterangan', 'like', '%' . $noPoTrim . '%');
                            })
                            ->delete();
                    }
                }
            } catch (\Throwable $e) {
                \Log::warning('[Stock] Rollback BarangKeluar saat hapus Data PO gagal', [
                    'error' => $e->getMessage(),
                    'no_po' => $suratJalan->no_po,
                    'po_id' => $suratJalan->id,
                ]);
            }

            // Sinkronkan Sisa Data PO: hapus entri sisa untuk no_po yang sama
            try {
                $noPoTrim = trim((string) $suratJalan->no_po);
                if ($noPoTrim !== '') {
                    SisaPOItem::where('no_po', $noPoTrim)->delete();
                }
            } catch (\Throwable $e) {
                \Log::warning('[SisaPO] Gagal menghapus entri sisa terkait saat hapus Data PO', [
                    'error' => $e->getMessage(),
                    'no_po' => $suratJalan->no_po,
                ]);
            }

            // Simpan konteks untuk placeholder invoice jika grup kosong
            $poNumber = (int) ($suratJalan->po_number ?? 0);
            $placeholderTanggal = $suratJalan->tanggal_po ?: now()->format('Y-m-d');
            $placeholderCustomerId = $suratJalan->customer_id ?? null;
            $placeholderCustomer = $suratJalan->customer ?? '-';

            $suratJalan->delete();

            // Jika setelah hapus tidak ada lagi PO untuk po_number ini, buat draft placeholder
            if ($poNumber > 0 && !\App\Models\PO::where('po_number', $poNumber)->exists()) {
                try {
                    \App\Models\PO::create([
                        'po_number'      => $poNumber,
                        'tanggal_po'     => $placeholderTanggal,
                        'customer_id'    => $placeholderCustomerId,
                        'customer'       => $placeholderCustomer,
                        'no_surat_jalan' => null,
                        'no_po'          => '-',
                        'no_invoice'     => null,
                        'produk_id'      => null,
                        'qty'            => 0,
                        'qty_jenis'      => 'PCS',
                        'harga'          => 0,
                        'total'          => 0,
                        'kendaraan'      => null,
                        'no_polisi'      => null,
                        'alamat_1'       => null,
                        'alamat_2'       => null,
                        'pengirim'       => null,
                        'status_approval'=> 'Pending',
                    ]);
                } catch (\Throwable $e) {
                    \Log::warning('[Invoice Placeholder] Gagal membuat draft placeholder setelah hapus PO', [
                        'error' => $e->getMessage(),
                        'po_number' => $poNumber,
                    ]);
                }
            }

            // Redirect dengan parameter bulan dan tahun yang sama
            $redirectUrl = route('suratjalan.index');
            if ($month && $year) {
                $redirectUrl .= "?month={$month}&year={$year}";
            }

            return redirect($redirectUrl)
                ->with('success', 'Surat jalan dan data Jatuh Tempo terkait berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('suratjalan.index')
                ->with('error', 'Gagal menghapus surat jalan: ' . $e->getMessage());
        }
    }

    /**
     * Export Surat Jalan ke Excel (1 baris per item)
     */
    public function export(Request $request)
    {
        try {
            Carbon::setLocale('id');

            // selected_ids dikirim sebagai JSON string dari form hidden di view
            // Jika kosong, ekspor semua data.
            $ids = null;
            if ($request->has('selected_ids')) {
                $decoded = json_decode($request->input('selected_ids'), true);
                if (is_array($decoded) && !empty($decoded)) {
                    $ids = $decoded;
                }
            }

            $fileName = ($ids ? 'Surat_Jalan_Selected_' : 'Surat_Jalan_All_') . date('Y-m-d_H-i-s') . '.xlsx';
            return Excel::download(new SuratJalanExport($ids), $fileName);
        } catch (\Exception $e) {
            \Log::error('Surat Jalan Export Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat export Excel: ' . $e->getMessage());
        }
    }

    /**
     * Mengambil data invoice berdasarkan ID yang dipilih
     */
    public function getInvoiceData(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return response()->json(['data' => []]);
        }

        // Ambil data PO beserta detail item dan produk agar invoice bisa menampilkan multi-produk
        $suratjalan = \App\Models\PO::with(['items.produk'])
            ->whereIn('id', $ids)
            ->get();

        return response()->json(['data' => $suratjalan]);
    }

    /**
     * Generate Invoice PDF (opsional untuk future development)
     */
    public function generateInvoicePDF(Request $request)
    {
        try {
            $raw = $request->input('selected_ids');
            // selected_ids dikirim sebagai JSON string dari front-end
            $selectedIds = is_array($raw) ? $raw : json_decode($raw, true);

            if (empty($selectedIds) || !is_array($selectedIds)) {
                return back()->with('error', 'Tidak ada data yang dipilih untuk invoice');
            }

            // Ambil PO dengan detail items dan produk untuk invoice multi-produk
            $pos = PO::with(['items.produk'])
                ->whereIn('id', $selectedIds)
                ->get();

            if ($pos->isEmpty()) {
                return back()->with('error', 'Data tidak ditemukan');
            }

            // Flatten items
            $allItems = [];
            foreach ($pos as $po) {
                foreach (($po->items ?? []) as $item) {
                    $allItems[] = $item;
                }
            }

            // Hitung totals
            $subtotal = collect($allItems)->sum(function ($it) {
                return (int) ($it->total ?? 0);
            });
            $totalQty = collect($allItems)->sum(function ($it) {
                return (int) ($it->qty ?? 0);
            });
            $ppn = (int) round($subtotal * 0.11);
            $grandTotal = $subtotal + $ppn;

            // Nomor invoice: gunakan no_invoice dari PO jika tersedia; fallback ke format lama
            $firstPo = $pos->first();
            // Gunakan tanggal PO (dari data) untuk konsistensi dengan tabel Surat Jalan
            $today = $firstPo && $firstPo->tanggal_po ? Carbon::parse($firstPo->tanggal_po) : Carbon::now();
            $today->locale('id');
            $invoiceNo = trim((string)($firstPo->no_invoice ?? ''));
            if ($invoiceNo === '') {
                $invoiceNo = rand(1000, 9999) . ' / CAM-GM / ' . $today->month . ' / ' . $today->format('y');
            }

            $invoiceDetails = [
                'invoice_no' => $invoiceNo,
                'invoice_date' => $today->translatedFormat('d F Y'),
                'customer' => $firstPo->customer,
                'address' => trim(($firstPo->alamat_1 ?? '') . ' ' . ($firstPo->alamat_2 ?? '')),
                'no_po' => $firstPo->no_po ?? '-',
                'no_surat_jalan' => $firstPo->no_surat_jalan ?? '-',
                'items' => $allItems,
                'subtotal' => $subtotal,
                'ppn' => $ppn,
                'grand_total' => $grandTotal,
                'total_qty' => $totalQty,
                'date_location' => $today->translatedFormat('d F Y'),
            ];

            // Hanya download PDF; tidak ada fallback HTML
            if (!class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
                return back()->with('error', 'DomPDF belum terpasang. Jalankan: composer require barryvdh/laravel-dompdf');
            }

            $forPdf = true;
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoice.pdf', compact('invoiceDetails', 'forPdf'))
                ->setPaper('a4', 'portrait');
            $fileName = 'Invoice_'.now()->format('Ymd_His').'.pdf';
            return $pdf->download($fileName);

        } catch (\Exception $e) {
            \Log::error('Invoice PDF Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat generate invoice: ' . $e->getMessage());
        }
    }

    /**
     * Update status approval Data PO
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'status_approval' => 'required|in:Pending,Accept',
            ]);

            $pos = SuratJalan::findOrFail($id);
            $oldStatus = $pos->status_approval ?? 'Pending';
            
            $pos->update([
                'status_approval' => $validated['status_approval'],
            ]);

            // Jika status berubah dari Pending ke Accept, buat Jatuh Tempo
            if ($oldStatus === 'Pending' && $validated['status_approval'] === 'Accept') {
                $this->createJatuhTempo($pos);
            }

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui',
                'new_status' => $validated['status_approval'],
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

    /**
     * Buat atau update Jatuh Tempo dari Data PO yang di-Accept
     * Hanya 1 Jatuh Tempo per Data PO meskipun status diubah berkali-kali
     */
    private function createJatuhTempo($pos)
    {
        try {
            // Cari customer untuk mendapatkan payment terms
            $customer = Customer::where('name', $pos->customer)->first();
            
            $tanggalInvoice = Carbon::parse($pos->tanggal_po);
            $termsDays = (int) ($customer->payment_terms_days ?? 0);
            
            if ($termsDays > 0) {
                $tanggalJatuhTempo = (clone $tanggalInvoice)->addDays($termsDays);
            } else {
                $tanggalJatuhTempo = (clone $tanggalInvoice)->addMonth();
            }

            // Gunakan po_number sebagai no_invoice (no urut invoice dari Data Invoice)
            // Jika tidak ada po_number, gunakan no_invoice dari database
            $invoiceKey = $pos->po_number ?? $pos->no_invoice;

            // CEK APAKAH SUDAH ADA JATUH TEMPO UNTUK DATA PO INI
            $existingJT = JatuhTempo::where('no_po', $pos->no_po)
                ->where('customer', $pos->customer)
                ->first();

            $jtPayload = [
                'no_invoice' => $invoiceKey,
                'no_po' => $pos->no_po,
                'customer' => $pos->customer,
                'tanggal_invoice' => $tanggalInvoice->format('Y-m-d'),
                'tanggal_jatuh_tempo' => $tanggalJatuhTempo->format('Y-m-d'),
                'jumlah_tagihan' => (int) ($pos->total ?? 0),
                'jumlah_terbayar' => 0,
                'sisa_tagihan' => (int) ($pos->total ?? 0),
                'status_pembayaran' => 'Belum Bayar',
                'status_approval' => 'Pending',
            ];

            if ($existingJT) {
                // UPDATE JATUH TEMPO YANG SUDAH ADA
                $existingJT->update($jtPayload);
                \Log::info('[JT] Updated existing JatuhTempo from Data PO Accept', [
                    'jt_id' => $existingJT->id,
                    'pos_id' => $pos->id,
                    'payload' => $jtPayload
                ]);
            } else {
                // BUAT JATUH TEMPO BARU
                $newJT = JatuhTempo::create($jtPayload);
                \Log::info('[JT] Created new JatuhTempo from Data PO Accept', [
                    'jt_id' => $newJT->id,
                    'pos_id' => $pos->id,
                    'payload' => $jtPayload
                ]);
            }
            
        } catch (\Throwable $e) {
            \Log::warning('[JT] Failed to create/update JatuhTempo from Data PO', [
                'error' => $e->getMessage(),
                'pos_id' => $pos->id ?? null,
            ]);
        }
    }
}
