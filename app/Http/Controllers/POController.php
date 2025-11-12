<?php

namespace App\Http\Controllers;

use App\Models\PO;
use App\Models\POItem;
use App\Models\SuratJalan;
use App\Models\Produk;
use App\Models\BarangKeluar;
use App\Models\Customer;
use App\Models\Pengirim; // Tambahkan model Pengirim
use App\Models\JatuhTempo; // Sinkronisasi Jatuh Tempo dari PO
// Invoice model tidak digunakan - data invoice disimpan di tabel POS
use App\Models\Setting;
use App\Models\SisaPOItem;
use App\Http\Requests\StorePORequest;
use App\Http\Requests\UpdatePORequest;
use App\Http\Requests\UpdateInvoiceRequest;
use App\Services\POService;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use PhpOffice\PhpSpreadsheet\IOFactory;

class POController extends Controller
{
    protected POService $poService;
    protected \App\Services\CacheService $cache;

    public function __construct(POService $poService, \App\Services\CacheService $cache)
    {
        $this->poService = $poService;
        $this->cache = $cache;
    }
    
    public function index()
    {
        // Gunakan cache untuk data yang jarang berubah
        $produks = $this->cache->getProducts();
        $customers = $this->cache->getCustomers();
        
        // PO data di-cache dengan TTL pendek karena sering berubah
        $pos = $this->cache->remember(
            $this->cache->key('pos_index'),
            \App\Services\CacheService::CACHE_SHORT,
            fn() => PO::with(['items.produk'])->latest()->get(),
            [\App\Services\CacheService::TAG_PO]
        );
        
        // Pengirim di-cache
        $pengirims = $this->cache->remember(
            $this->cache->key('pengirims_list'),
            \App\Services\CacheService::CACHE_MEDIUM,
            fn() => Pengirim::select('nama','kendaraan','no_polisi')
                ->whereNotNull('nama')
                ->where('nama', '!=', '')
                ->orderBy('nama', 'asc')
                ->get(),
            ['pengirims']
        );

        return view('po.index', compact('pos', 'produks', 'customers', 'pengirims'));
    }

    public function create()
    {
        // Guard: Form Create PO hanya boleh diakses dari Data Invoice (double click)
        $from = request('from');
        $invoiceNumber = request('invoice_number');
        if ($from !== 'invoice' || empty($invoiceNumber)) {
            return redirect()->route('invoice.index')
                ->with('error', 'Akses formulir PO hanya melalui Data Invoice (double click pada nomor urut).');
        }
        
        // Gunakan cache untuk data master
        $produks = $this->cache->getProducts();
        $customers = $this->cache->getCustomers();
        
        // Pengirim dari cache
        $pengirims = $this->cache->remember(
            $this->cache->key('pengirims_list'),
            \App\Services\CacheService::CACHE_MEDIUM,
            fn() => Pengirim::select('nama', 'kendaraan', 'no_polisi')
                ->whereNotNull('nama')
                ->where('nama', '!=', '')
                ->orderBy('nama', 'asc')
                ->get(),
            ['pengirims']
        );

        // PO data dengan cache
        $pos = $this->cache->remember(
            $this->cache->key('pos_index'),
            \App\Services\CacheService::CACHE_SHORT,
            fn() => PO::with(['items.produk'])->latest()->get(),
            [\App\Services\CacheService::TAG_PO]
        );

        // Prefill tanggal dari draft POS (no urut terkait) atau fallback ke hari ini
        $prefillTanggal = null;
        $draft = PO::where('no_invoice', (string)$invoiceNumber)->orderByDesc('id')->first();
        if ($draft && !empty($draft->tanggal_po)) {
            try {
                $prefillTanggal = \Carbon\Carbon::parse($draft->tanggal_po)->format('Y-m-d');
            } catch (\Throwable $e) {
                $prefillTanggal = now()->format('Y-m-d');
            }
        } else {
            $prefillTanggal = now()->format('Y-m-d');
        }

        // Siapkan prefill No Surat Jalan dari code_number customer (format: BAG1-BAG2/BAG3)
        $sjCodeParts = [null, null, null];
        $sjNomor = $sjPt = $sjTahun = null;
        if ($draft && $draft->customer_id) {
            try {
                $cust = Customer::find($draft->customer_id);
                if ($cust && !empty($cust->code_number)) {
                    $leftRight = explode('/', $cust->code_number);
                    $left = $leftRight[0] ?? '';
                    $p3 = $leftRight[1] ?? '';
                    $lr = explode('-', $left);
                    $p1 = $lr[0] ?? '';
                    $p2 = $lr[1] ?? '';
                    $sjCodeParts = [$p1 ?: null, $p2 ?: null, $p3 ?: null];
                    $sjNomor = $p1 ?: null;
                    $sjPt    = $p2 ?: null;
                    $sjTahun = $p3 ?: null;
                }
            } catch (\Throwable $e) { /* ignore */ }
        }

        // Prefill alamat dari data customer saat masuk dari Data Invoice
        if ($draft && $draft->customer_id) {
            try {
                $cust = $cust ?? Customer::find($draft->customer_id);
                if ($cust) {
                    if (empty($draft->alamat_1) || trim((string)$draft->alamat_1) === '-' ) {
                        $draft->alamat_1 = $cust->address_1 ?? '';
                    }
                    if (empty($draft->alamat_2) || trim((string)$draft->alamat_2) === '-' ) {
                        $draft->alamat_2 = $cust->address_2 ?? '';
                    }
                }
            } catch (\Throwable $e) { /* ignore */ }
        }

        // Kirim draft sebagai $po agar binding form (customer_id, dsb) otomatis terpilih
        $po = $draft;
        return view('po.index', compact('pos', 'produks', 'customers', 'pengirims', 'prefillTanggal', 'po', 'sjCodeParts', 'sjNomor', 'sjPt', 'sjTahun'));
    }

    public function store(StorePORequest $request)
    {
        $data = $request->validated();

        // Default tanggal_po ke hari ini jika tidak dikirim dari form
        if (empty($data['tanggal_po'])) {
            $data['tanggal_po'] = now()->format('Y-m-d');
        }

        // Build nomor menggunakan service
        $noSuratJalan = $this->poService->buildNoSuratJalan($data);
        $noInvoice = $this->poService->buildNoInvoice($data);
        
        // Resolve customer
        $customer = !empty($data['customer_id']) ? Customer::find($data['customer_id']) : null;
        $customerName = $this->poService->resolveCustomerName($data, $request);
        
        // Resolve addresses
        [$data['address_1'], $data['address_2']] = $this->poService->resolveAddresses($data, $customer);

        // Simpan ke database PO (header) + items dalam transaksi
        // Track auto-split agar bisa membentuk pesan sukses terpadu di luar transaksi
        $hadSplit = false; // ada sisa ke PO Belum Terkirim
        $sisaCount = 0;    // total qty sisa (pcs/set, sesuai input)

        $po = DB::transaction(function () use ($data, $customerName, $noSuratJalan, $noInvoice, $customer, &$hadSplit, &$sisaCount) {
            // Process items menggunakan service
            $items = $this->poService->processItems($data['items']);

            // Validasi dan split stok menggunakan service
            $splitResult = $this->poService->validateAndSplitStock($items, $data, $customerName);
            $items = $splitResult['items'];
            $sisaItems = $splitResult['sisa_items'];
            $splitMessages = $splitResult['split_messages'];
            $hadSplit = $splitResult['had_split'];
            $sisaCount = $splitResult['sisa_count'];

            // Header mengambil item pertama untuk kolom legacy
            $first = !empty($items) ? $items[0] : null;
            $sumTotal = (int) array_sum(array_column($items, 'total'));

            // Jika tidak ada item yang masuk ke PO (semua stok 0), JANGAN buat PO record
            if (empty($items)) {
                $this->poService->saveSisaItems($sisaItems);
                if (!empty($splitMessages)) {
                    session()->flash('split_messages', $splitMessages);
                }
                return null; // Akan dihandle di luar transaction
            }

            // Cari draft berdasarkan no_invoice (nomor urut invoice)
            $po = null;
            $shouldUpdateDraft = false;
            if (!empty($data['invoice_number'])) {
                $po = PO::where('no_invoice', (string)$data['invoice_number'])->orderByDesc('id')->first();
                if ($po) {
                    $hasItems = $po->items()->count() > 0;
                    $isNoPoEmpty = empty(trim((string)($po->no_po))) || trim((string)($po->no_po)) === '-';
                    // Hanya update record draft awal (no_po kosong/'-' dan belum punya item)
                    $shouldUpdateDraft = ($isNoPoEmpty && !$hasItems);
                }
            }

            if ($po && $shouldUpdateDraft) {
                // UPDATE draft awal agar tidak membuat baris kosong
                $po->update([
                    'no_invoice'    => $data['invoice_number'], // Nomor urut invoice
                    'tanggal_po'    => $data['tanggal_po'],
                    'customer'      => $customerName,
                    'no_surat_jalan'=> $noSuratJalan,
                    'no_po'         => $data['no_po'],
                    'produk_id'      => $first['produk_id'] ?? null,
                    'qty'            => $first['qty'] ?? 0,
                    'qty_jenis'      => $first['qty_jenis'] ?? 'PCS',
                    'harga'          => $first['harga'] ?? 0,
                    'total'          => $sumTotal,
                    'kendaraan'      => $data['kendaraan'],
                    'no_polisi'      => $data['no_polisi'],
                    'alamat_1'       => $data['address_1'],
                    'alamat_2'       => $data['address_2'] ?? null,
                    'pengirim'       => $data['pengirim'] ?? null,
                ]);

                // Sync items dan catat barang keluar
                $this->poService->syncPOItems($po, $items);
                $this->poService->recordBarangKeluar($items, $data['no_po'], $data['tanggal_po']);
                $this->poService->pruneBarangKeluar(50);
            } else {
                // CREATE baris baru meskipun no_invoice sama (mendukung input >1x untuk No Urut yang sama)
                $po = PO::create([
                    'no_invoice'    => $data['invoice_number'] ?? null,
                    'tanggal_po'    => $data['tanggal_po'],
                    'customer'      => $customerName,
                    'no_surat_jalan'=> $noSuratJalan,
                    'no_po'         => $data['no_po'],
                    'produk_id'      => $first['produk_id'] ?? null,
                    'qty'            => $first['qty'] ?? 0,
                    'qty_jenis'      => $first['qty_jenis'] ?? 'PCS',
                    'harga'          => $first['harga'] ?? 0,
                    'total'          => $sumTotal,
                    'kendaraan'      => $data['kendaraan'],
                    'no_polisi'      => $data['no_polisi'],
                    'alamat_1'       => $data['address_1'],
                    'alamat_2'       => $data['address_2'] ?? null,
                    'pengirim'       => $data['pengirim'] ?? null,
                ]);

                // Sync items dan catat barang keluar
                $this->poService->syncPOItems($po, $items);
                $this->poService->recordBarangKeluar($items, $data['no_po'], $data['tanggal_po']);
                $this->poService->pruneBarangKeluar(50);
            }

            // Simpan sisa items ke tabel sisa_po_items jika ada
            $this->poService->saveSisaItems($sisaItems);

            return $po;
        });

        // Simpan pesan split ke session untuk ditampilkan sebagai notifikasi
        if (!empty($splitMessages)) {
            session()->flash('split_messages', $splitMessages);
        }

        // Bersihkan reserved jika nomor ini ada di cache (nomor sudah resmi tersimpan)
        if (!empty($data['invoice_number'])) {
            $reserved = (array) (session('invoice_reserved_numbers', []));
            $reserved = array_values(array_filter($reserved, fn($v) => $v !== $data['invoice_number']));
            session(['invoice_reserved_numbers' => $reserved]);
            // Jika epoch aktif, catat nomor ini sebagai saved dalam epoch
            if ((bool) session('invoice_epoch_active', false)) {
                $epochSaved = (array) (session('invoice_epoch_saved_numbers', []));
                $epochSaved[] = $data['invoice_number'];
                $epochSaved = array_values(array_slice(array_unique($epochSaved), -500));
                session(['invoice_epoch_saved_numbers' => $epochSaved]);
            }
        }

        // Handle kasus khusus jika semua produk stok 0
        if ($po === null) {
            // Redirect kembali ke form dengan pesan bahwa semua masuk ke Sisa Data PO
            $from = $request->input('from', 'invoice');
            $invoiceNumber = $request->input('invoice_number');
            return redirect()
                ->route('po.create', [
                    'from' => $from,
                    'invoice_number' => $invoiceNumber,
                    'tanggal_po' => $data['tanggal_po'],
                    'reset_fields' => '1', // Parameter untuk trigger reset form
                ])
                ->with('success', 'Data PO disimpan! Semua produk masuk ke Sisa Data PO karena stok habis.');
        }

        // === Ekspor ke Excel ===
        $this->poService->exportToExcel($po, $noSuratJalan);

        // === Sinkronisasi ke Jatuh Tempo ===
        $invoiceKey = $data['invoice_number'] ?? $po->no_invoice;
        $this->poService->syncToJatuhTempo($po, $customer, $invoiceKey);

        // Selalu tetap berada di Form Input PO setelah simpan
        $from = $request->input('from', 'invoice');
        $invoiceNumber = $request->input('invoice_number') ?? $po->no_invoice;
        // Bentuk pesan sukses terpadu
        $successMsg = 'Data PO berhasil disimpan! Tetap di Form Input PO untuk melanjutkan.';
        if ($hadSplit && $sisaCount > 0) {
            $successMsg .= ' Sebagian pesanan belum dapat diproses. Total ' . (int)$sisaCount . ' item dialihkan ke PO Belum Terkirim. Anda bisa meninjau dan melengkapi stok pada menu PO Belum Terkirim.';
        } else {
            $successMsg .= ' Data telah tersimpan dan siap untuk dicetak atau dilihat di Surat Jalan.';
        }

        return redirect()
            ->route('po.create', [
                'from' => $from ?: 'invoice',
                'invoice_number' => $invoiceNumber,
                'tanggal_po' => \Carbon\Carbon::parse($po->tanggal_po)->format('Y-m-d'),
                'reset_fields' => '1', // Parameter untuk trigger reset form
            ])
            ->with('success', $successMsg);
    }

    /**
     * Toggle status approval PO (Pending <-> Accept) dan sinkronisasi Jatuh Tempo.
     */
    public function toggleStatus(Request $request, PO $po)
    {
        $invoiceKey = $po->no_invoice;
        $current = (string)($po->status_approval ?? 'Pending');
        $new = $current === 'Accept' ? 'Pending' : 'Accept';

        // CEK: Jika ada Jatuh Tempo dengan status Lunas (Accept), BLOKIR perubahan status
        if ($this->poService->isInvoiceLunas($invoiceKey)) {
            return response()->json([
                'success' => false,
                'message' => '❌ Status tidak dapat diubah karena Jatuh Tempo sudah berstatus LUNAS (Accept).'
            ], 403);
        }

        DB::transaction(function () use ($po, $new, $invoiceKey) {
            // Update hanya baris PO yang diklik
            $po->update(['status_approval' => $new]);

            if ($new === 'Accept') {
                // Sinkronkan SEMUA PO dengan no_invoice yang sama ke 1 entry Jatuh Tempo
                try {
                    // Ambil semua PO dengan no_invoice yang sama dan status Accept
                    $allAcceptedPos = PO::where('no_invoice', $po->no_invoice)
                        ->where('status_approval', 'Accept')
                        ->get();

                    if ($allAcceptedPos->isNotEmpty()) {
                        $customer = Customer::find($po->customer_id);
                        $tanggalInvoice = \Carbon\Carbon::parse($po->tanggal_po);
                        $termsDays = (int) (($customer->payment_terms_days ?? 0));
                        $tanggalJatuhTempo = $termsDays > 0
                            ? (clone $tanggalInvoice)->addDays($termsDays)
                            : (clone $tanggalInvoice)->addMonth();

                        // Gabungkan semua No PO dan total tagihan
                        $allNoPo = $allAcceptedPos->pluck('no_po')->filter()->implode(', ');
                        $totalTagihan = $allAcceptedPos->sum('total');
                        
                        // Cek apakah sudah ada entry jatuh tempo untuk invoice ini
                        $existingJT = \App\Models\JatuhTempo::where('no_invoice', $invoiceKey)->first();
                        $jumlahTerbayar = $existingJT ? (int)($existingJT->jumlah_terbayar ?? 0) : 0;

                        $payload = [
                            'no_invoice' => $invoiceKey,
                            'no_po' => $allNoPo, // Gabungan semua No PO
                            'customer' => $po->customer,
                            'tanggal_invoice' => $tanggalInvoice->format('Y-m-d'),
                            'tanggal_jatuh_tempo' => $tanggalJatuhTempo->format('Y-m-d'),
                            'jumlah_tagihan' => (int) $totalTagihan, // Total dari semua PO
                            'jumlah_terbayar' => $jumlahTerbayar,
                            'sisa_tagihan' => max(0, (int)$totalTagihan - $jumlahTerbayar),
                            'status_pembayaran' => $jumlahTerbayar >= $totalTagihan ? 'Lunas' : ($jumlahTerbayar > 0 ? 'Sebagian' : 'Belum Bayar'),
                        ];

                        \App\Models\JatuhTempo::updateOrCreate(
                            ['no_invoice' => $invoiceKey],
                            $payload
                        );
                    }
                } catch (\Throwable $e) {
                    \Log::warning('[JT] Toggle group sync failed', ['error' => $e->getMessage()]);
                }
            } else {
                // Toggle ke Pending: cek apakah masih ada PO lain yang Accept
                try {
                    $remainingAcceptedPos = PO::where('no_invoice', $po->no_invoice)
                        ->where('status_approval', 'Accept')
                        ->where('id', '!=', $po->id)
                        ->get();

                    if ($remainingAcceptedPos->isEmpty()) {
                        // Tidak ada PO Accept lagi, hapus jatuh tempo
                        \App\Models\JatuhTempo::where('no_invoice', $invoiceKey)->delete();
                    } else {
                        // Masih ada PO Accept lain, update jatuh tempo dengan data yang tersisa
                        $allNoPo = $remainingAcceptedPos->pluck('no_po')->filter()->implode(', ');
                        $totalTagihan = $remainingAcceptedPos->sum('total');
                        
                        $existingJT = \App\Models\JatuhTempo::where('no_invoice', $invoiceKey)->first();
                        if ($existingJT) {
                            $jumlahTerbayar = (int)($existingJT->jumlah_terbayar ?? 0);
                            $existingJT->update([
                                'no_po' => $allNoPo,
                                'jumlah_tagihan' => (int) $totalTagihan,
                                'sisa_tagihan' => max(0, (int)$totalTagihan - $jumlahTerbayar),
                                'status_pembayaran' => $jumlahTerbayar >= $totalTagihan ? 'Lunas' : ($jumlahTerbayar > 0 ? 'Sebagian' : 'Belum Bayar'),
                            ]);
                        }
                    }
                } catch (\Throwable $e) {
                    \Log::warning('[JT] Delete JT on single toggle back failed', ['error' => $e->getMessage()]);
                }
            }
        });

        $response = [
            'success' => true,
            'status' => $new,
            'message' => $new === 'Accept' ? 'PO di-set ke Accept dan disinkronkan ke Jatuh Tempo.' : 'PO dikembalikan ke Pending dan Jatuh Tempo-nya dihapus.',
        ];

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($response);
        }

        return redirect()->back()->with('success', $response['message']);
    }

    public function edit($id)
    {
        $po         = PO::findOrFail($id);
        $pos        = PO::with(['produkRel'])->latest()->get();
        $produks    = Produk::all();
        $customers  = Customer::all();
        
        // CHANGE: Ambil data pengirim dari tabel pengirim beserta kendaraan & no_polisi
        $pengirims = Pengirim::select('nama', 'kendaraan', 'no_polisi')
            ->whereNotNull('nama')
            ->where('nama', '!=', '')
            ->orderBy('nama', 'asc')
            ->get();

        return view('po.index', compact('po', 'pos', 'produks', 'customers', 'pengirims'));
    }

    public function update(UpdatePORequest $request, PO $po)
    {
        // CEK: Jika ada Jatuh Tempo dengan status Lunas (Accept), BLOKIR edit
        if ($this->poService->isInvoiceLunas($po->no_invoice)) {
            return redirect()->back()->with('error', '❌ Data tidak dapat diubah karena Jatuh Tempo sudah berstatus LUNAS (Accept).');
        }

        $data = $request->validated();

        // Build nomor menggunakan service
        $noSuratJalan = $this->poService->buildNoSuratJalan($data);
        $noInvoice = $this->poService->buildNoInvoice($data);

        // Resolve customer dan addresses
        $customer = Customer::find($data['customer_id']);
        $customerName = $customer?->name ?? '';
        [$data['address_1'], $data['address_2']] = $this->poService->resolveAddresses($data, $customer);

        DB::transaction(function () use ($po, $data, $customerName, $noSuratJalan, $noInvoice) {
            // Process dan validasi items
            $items = $this->poService->processItems($data['items']);
            $this->poService->validateStockForUpdate($items);

            $first = $items->first();
            $sumTotal = (int) $items->sum(fn ($it) => (int) ($it['total'] ?? 0));

            // Pastikan nomor urut tetap ada
            $updatePayload = [
                'no_surat_jalan' => $noSuratJalan,
                'no_po'          => $data['no_po'],
                'customer_id'    => $data['customer_id'],
                'customer'       => $customerName,
                'tanggal_po'     => $data['tanggal_po'],
                'produk_id'      => $first['produk_id'] ?? null,
                'qty'            => $first['qty'] ?? 0,
                'qty_jenis'      => $first['qty_jenis'] ?? 'PCS',
                'harga'          => $first['harga'] ?? 0,
                'total'          => $sumTotal,
                'kendaraan'      => $data['kendaraan'],
                'no_polisi'      => $data['no_polisi'],
                'alamat_1'       => $data['address_1'],
                'alamat_2'       => $data['address_2'] ?? null,
                'pengirim'       => $data['pengirim'] ?? null,
            ];
            // Set no_invoice jika belum ada
            if (empty($po->no_invoice) && !empty($data['invoice_number'])) {
                $updatePayload['no_invoice'] = $data['invoice_number'];
            }

            $po->update($updatePayload);

            // Sync items dan refresh barang keluar
            $this->poService->syncPOItems($po, $items);
            
            // Hapus BarangKeluar lama untuk no_po ini
            try {
                $oldNoPo = $po->getOriginal('no_po') ?: $po->no_po;
                $newNoPo = $data['no_po'] ?? $po->no_po;
                BarangKeluar::whereIn('keterangan', [
                    'Auto Keluar dari PO ' . (string) $oldNoPo,
                    'Auto Keluar dari PO ' . (string) $newNoPo,
                ])->delete();
            } catch (\Throwable $e) { /* ignore */ }
            
            // Record barang keluar baru
            $this->poService->recordBarangKeluar($items, $data['no_po'], $data['tanggal_po']);
            $this->poService->pruneBarangKeluar(50);
        });

        // Bersihkan reserved jika nomor ini ada di cache (nomor sudah resmi tersimpan)
        if (!empty($data['invoice_number'])) {
            $reserved = (array) (\Cache::get('invoice_reserved_numbers', []));
            $reserved = array_values(array_filter($reserved, fn($v) => $v !== $data['invoice_number']));
            \Cache::forever('invoice_reserved_numbers', $reserved);
        }

        // === Sinkronisasi ke Jatuh Tempo (update) ===
        try {
            $invoiceKey = $noInvoice ?: $noSuratJalan;
            $tanggalInvoice = \Carbon\Carbon::parse($po->tanggal_po);
            // Gunakan payment_terms_days dari customer jika tersedia, fallback +1 bulan
            $termsDays = (int) (($customer->payment_terms_days ?? 0));
            if ($termsDays > 0) {
                $tanggalJatuhTempo = (clone $tanggalInvoice)->addDays($termsDays);
            } else {
                $tanggalJatuhTempo = (clone $tanggalInvoice)->addMonth();
            }

            // Pertahankan jumlah_terbayar jika sudah ada data sebelumnya
            $existingJT = JatuhTempo::where('no_invoice', $invoiceKey)->first();
            $jumlahTerbayar = $existingJT ? (int) ($existingJT->jumlah_terbayar ?? 0) : 0;
            $jumlahTagihan = (int) ($po->total ?? 0);

            JatuhTempo::updateOrCreate(
                ['no_invoice' => $invoiceKey],
                [
                    'no_po' => $po->no_po,
                    'customer' => $po->customer,
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
            \Log::warning('Sync JatuhTempo (update) gagal: ' . $e->getMessage());
        }

        // Redirect: tetap berada di Form Input PO
        $from = $request->input('from', 'invoice');
        $invoiceNumber = $request->input('invoice_number') ?? $po->no_invoice;
        return redirect()
            ->route('po.create', [
                'from' => $from,
                'invoice_number' => $invoiceNumber,
                'tanggal_po' => \Carbon\Carbon::parse($po->tanggal_po)->format('Y-m-d'),
            ])
            ->with('success', 'Data PO berhasil disimpan! 🎉');
    }

    public function destroy(PO $po)
    {
        // CEK: Jika ada Jatuh Tempo dengan status Lunas (Accept), BLOKIR hapus
        if ($this->poService->isInvoiceLunas($po->no_invoice)) {
            return redirect()->back()->with('error', '❌ Data tidak dapat dihapus karena Jatuh Tempo sudah berstatus LUNAS (Accept).');
        }

        // Lepas nomor dari reserved cache & epoch agar bisa digunakan lagi
        $num = $po->no_invoice;

        // HAPUS SELURUH GRUP (Data Invoice) SEKALI KLIK dari halaman Data Invoice
        $from = request('from');
        $referer = request()->headers->get('referer');
        $isInvoiceContext = ($from === 'invoice') || ($referer && str_contains($referer, '/po/invoices'));
        $isGroup = request()->boolean('group');
        if ($isInvoiceContext && !empty($po->no_invoice)) {
            DB::transaction(function() use ($po, $num) {
                $group = PO::where('no_invoice', $po->no_invoice)->get();

                // Kumpulkan semua no_po valid untuk penghapusan JatuhTempo berdasarkan no_po
                $noPoList = $group->pluck('no_po')->filter(function($v){
                    $t = trim((string)$v);
                    return $t !== '' && $t !== '-';
                })->values();

                foreach ($group as $gp) {
                    // Rollback stok menggunakan service
                    $this->poService->rollbackStock($gp);
                    
                    // Hapus entitas PO
                    $gp->delete();
                }

                // Hapus JatuhTempo berdasarkan no_invoice dan daftar no_po dalam grup
                try {
                    $noInvKey = $po->no_invoice;
                    if ($noInvKey !== '') {
                        // Hapus entri Jatuh Tempo terkait (tabel invoices sudah tidak digunakan)
                        JatuhTempo::where('no_invoice', $noInvKey)->delete();
                        JatuhTempo::whereRaw('TRIM(no_invoice) = ?', [$noInvKey])->delete();
                    }
                    if ($noPoList->isNotEmpty()) {
                        JatuhTempo::whereIn('no_po', $noPoList->all())->delete();
                        foreach ($noPoList as $np) {
                            JatuhTempo::whereRaw('TRIM(no_po) = ?', [trim((string)$np)])->delete();
                        }
                    }
                } catch (\Throwable $e) { /* ignore */ }

                // Bersihkan nomor reserved/epoch sekali saja untuk nomor ini
                if (!empty($num)) {
                    $reserved = (array) (session('invoice_reserved_numbers', []));
                    $reserved = array_values(array_filter($reserved, fn($v) => $v !== $num));
                    session(['invoice_reserved_numbers' => $reserved]);
                    if ((bool) session('invoice_epoch_active', false)) {
                        $epochSaved = (array) (session('invoice_epoch_saved_numbers', []));
                        $epochSaved = array_values(array_filter($epochSaved, fn($v) => $v !== $num));
                        session(['invoice_epoch_saved_numbers' => $epochSaved]);
                    }
                }
            });

            return redirect()->route('invoice.index')->with('success', 'Seluruh baris untuk No Invoice ' . $num . ' berhasil dihapus.');
        }
        // Rollback stok menggunakan service
        $this->poService->rollbackStock($po);
        // Cascade delete: hapus JatuhTempo terkait no_invoice dan no_po
        try {
            $noInvKey = $po->no_invoice;

            if (!empty($noInvKey)) {
                JatuhTempo::where('no_invoice', $noInvKey)->delete();
                JatuhTempo::whereRaw('TRIM(no_invoice) = ?', [trim((string)$noInvKey)])->delete();
            }

            // Hapus juga berdasarkan semua no_po dalam grup nomor urut yang sama
            if (!empty($po->no_invoice)) {
                $groupNoPos = PO::where('no_invoice', $po->no_invoice)
                    ->whereNotNull('no_po')
                    ->pluck('no_po')
                    ->filter(fn($v) => trim((string)$v) !== '' && trim((string)$v) !== '-')
                    ->values();
                if ($groupNoPos->isNotEmpty()) {
                    JatuhTempo::whereIn('no_po', $groupNoPos->all())->delete();
                    foreach ($groupNoPos as $np) {
                        $npTrim = trim((string)$np);
                        JatuhTempo::whereRaw('TRIM(no_po) = ?', [$npTrim])->delete();
                    }
                }
            } elseif (!empty($po->no_po) && trim((string)$po->no_po) !== '-') {
                $npTrim = trim((string)$po->no_po);
                JatuhTempo::where('no_po', $npTrim)->delete();
                JatuhTempo::whereRaw('TRIM(no_po) = ?', [$npTrim])->delete();
            }
        } catch (\Throwable $e) {
            \Log::warning('[JT] Gagal menghapus JatuhTempo via POController::destroy', [
                'error' => $e->getMessage(),
                'po_id' => $po->id,
                'no_invoice' => $po->no_invoice,
                'no_po' => $po->no_po,
            ]);
        }

        $po->delete();
        if (!empty($num)) {
            $reserved = (array) (session('invoice_reserved_numbers', []));
            $reserved = array_values(array_filter($reserved, fn($v) => $v !== $num));
            session(['invoice_reserved_numbers' => $reserved]);
            if ((bool) session('invoice_epoch_active', false)) {
                $epochSaved = (array) (session('invoice_epoch_saved_numbers', []));
                $epochSaved = array_values(array_filter($epochSaved, fn($v) => $v !== $num));
                session(['invoice_epoch_saved_numbers' => $epochSaved]);
            }
        }
        // Tetap berada di halaman Data Invoice jika penghapusan dilakukan dari sana
        if ($isInvoiceContext) {
            return redirect()->route('invoice.index')->with('success', 'Data Invoice berhasil dihapus.');
        }
        return redirect()->route('po.index')->with('success', 'Data PO berhasil dihapus.');
    }

    private function syncToJatuhTempo(PO $po)
    {
        try {
            $customer = Customer::where('nama', $po->customer)->first();
            
            // Gunakan no_invoice sebagai no_invoice di Jatuh Tempo
            $invoiceKey = $po->no_invoice;
            $tanggalInvoice = \Carbon\Carbon::parse($po->tanggal_po);
            
            // Gunakan payment_terms_days dari customer jika tersedia, fallback +1 bulan
            $termsDays = (int) (($customer->payment_terms_days ?? 0));
            if ($termsDays > 0) {
                $tanggalJatuhTempo = (clone $tanggalInvoice)->addDays($termsDays);
            } else {
                $tanggalJatuhTempo = (clone $tanggalInvoice)->addMonth();
            }

            // CEK APAKAH SUDAH ADA JATUH TEMPO UNTUK DATA PO INI
            $existingJT = JatuhTempo::where('no_po', $po->no_po)
                ->where('customer', $po->customer)
                ->first();

            $jtPayload = [
                'no_invoice' => $invoiceKey,
                'no_po' => $po->no_po,
                'customer' => $po->customer,
                'tanggal_invoice' => $tanggalInvoice->format('Y-m-d'),
                'tanggal_jatuh_tempo' => $tanggalJatuhTempo->format('Y-m-d'),
                'nilai_invoice' => $po->items->sum('total') ?? 0,
                'status_pembayaran' => 'Belum Bayar',
            ];

            if ($existingJT) {
                $existingJT->update($jtPayload);
            } else {
                JatuhTempo::create($jtPayload);
            }
            
        } catch (\Exception $e) {
            Log::error('Error syncing to Jatuh Tempo: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Tampilkan halaman Data Invoice (berdasarkan tabel POS/PO)
     */
    public function invoiceIndex()
    {
        // Cache invoice list dengan TTL pendek karena sering berubah
        $invoices = $this->cache->remember(
            $this->cache->key('invoices_grouped'),
            \App\Services\CacheService::CACHE_SHORT,
            function() {
                return PO::query()
                    ->select('*')
                    ->whereNotNull('no_invoice')
                    ->where('no_invoice', '!=', '')
                    ->get()
                    ->groupBy('no_invoice')
                    ->map(function ($group) {
                // Gunakan baris terbaru berdasarkan id untuk konsistensi
                $poLatest = $group->sortByDesc('id')->first();

                // Cari customer yang tidak kosong/dash/draft dengan prioritas terbaru
                $customerFromGroup = $group
                    ->sortByDesc('id')
                    ->pluck('customer')
                    ->first(function ($val) {
                        $v = is_string($val) ? trim(strtolower($val)) : '';
                        return $v !== '' && $v !== '-' && $v !== 'draft';
                    });

                $customer = $customerFromGroup ?? ($poLatest->customer ?? '');
                if (is_string($customer) && strtolower(trim($customer)) === 'draft') {
                    $customer = '-';
                }
                if (!is_string($customer) || trim($customer) === '') {
                    $customer = '-';
                }

                // Hitung total PO dengan no_invoice yang sama dan no_po valid (bukan placeholder)
                $totalPO = PO::query()
                    ->where('no_invoice', $poLatest->no_invoice)
                    ->whereNotNull('no_po')
                    ->where('no_po', '!=', '-')
                    ->whereRaw("TRIM(no_po) != ''")
                    ->count();

                return (object) [
                    'id'        => $poLatest->id,
                    'tanggal'   => $poLatest->tanggal_po ? \Carbon\Carbon::parse($poLatest->tanggal_po)->format('d/m/Y') : '-',
                    'no_urut'   => $poLatest->no_invoice,
                    'customer'  => $customer,
                    'no_po'     => $poLatest->no_po,
                    'total_po'  => $totalPO,
                    'barang'    => null, // Tidak perlu tampil individual
                    'qty'       => $poLatest->qty,
                    'harga'     => $poLatest->harga,
                    'total'     => $poLatest->total,
                    'status_approval' => $poLatest->status_approval ?? 'Pending',
                ];
            })
            ->sortBy(function ($invoice) {
                // Sort berdasarkan no_urut secara numerik (1, 2, 3... 10, 11)
                return (int) $invoice->no_urut;
            })
            ->values(); // Reset keys dari sortBy
            },
            [\App\Services\CacheService::TAG_INVOICE, \App\Services\CacheService::TAG_PO]
        );

        // Cache customers
        $customers = $this->cache->getCustomers();
        return view('po.invoice_index', compact('invoices', 'customers'));
    }

    /**
     * Hitung nomor invoice berikutnya dan arahkan ke form input PO (prefill)
     */
    public function invoiceNext()
    {
        $today = now();

        // Jika reset diminta: aktifkan epoch reset (per user/session) dan kosongkan daftar
        if (request()->boolean('reset')) {
            session(['invoice_epoch_active' => true]);
            session(['invoice_reserved_numbers' => []]);
            session(['invoice_epoch_saved_numbers' => []]);
            // Pada epoch aktif, next dihitung dari set kosong => 1
            $nextNomor = 1;
        } else {
            $epochActive = (bool) session('invoice_epoch_active', false);
            $reserved = (array) (session('invoice_reserved_numbers', []));
            if ($epochActive) {
                // Saat epoch aktif, abaikan DB lama. Pakai gabungan reserved + epoch_saved_numbers
                $epochSaved = (array) (session('invoice_epoch_saved_numbers', []));
                $used = [];
                foreach ($reserved as $r) { $used[(int)$r] = true; }
                foreach ($epochSaved as $s) { $used[(int)$s] = true; }
                $nextNomor = 1;
                while (isset($used[$nextNomor])) { $nextNomor++; }
                // Reserve
                $reserved[] = $nextNomor;
                $reserved = array_values(array_slice(array_unique(array_map('intval', $reserved)), -200));
                session(['invoice_reserved_numbers' => $reserved]);
            } else {
                // Mode normal: MEX atas DB + reserved
                $usedNumbers = PO::query()
                    ->whereNotNull('no_invoice')
                    ->where('no_invoice', '!=', '')
                    ->pluck('no_invoice')
                    ->toArray();
                // Convert to integers untuk perhitungan next
                $used = array_fill_keys(array_map('intval', $usedNumbers), true);
                foreach ($reserved as $r) { $used[(int)$r] = true; }
                $nextNomor = 1;
                while (isset($used[$nextNomor])) { $nextNomor++; }
                $reserved[] = $nextNomor;
                $reserved = array_values(array_slice(array_unique(array_map('intval', $reserved)), -200));
                session(['invoice_reserved_numbers' => $reserved]);
            }
        }

        // Jika diminta JSON (AJAX), kembalikan data tanpa redirect
        if (request()->wantsJson() || request()->boolean('json')) {
            return response()->json([
                'success' => true,
                'next_nomor' => $nextNomor,
                'tanggal' => $today->format('Y-m-d'),
                'tanggal_display' => $today->format('d/m/Y'),
            ]);
        }

        // Default: Redirect ke form PO dengan prefill
        return redirect()->route('po.create', [
            'no_invoice_nomor' => $nextNomor,
            'tanggal_po' => $today->format('Y-m-d'),
        ]);
    }

    /**
     * Quick-create: membuat draft baris invoice minimal.
     * Menghormati optional next_hint (jika tersedia dan belum dipakai), serta membawa customer_id/customer.
     */
    public function invoiceQuickCreate(Request $request)
    {
        try {
            $hint       = (int) ($request->input('next_hint') ?? 0);
            $customerId = $request->input('customer_id');
            $customerNm = $request->input('customer');

            // Kumpulkan SEMUA nomor terpakai secara robust
            $raw = PO::query()
                ->whereNotNull('no_invoice')
                ->where('no_invoice', '!=', '')
                ->pluck('no_invoice')
                ->toArray();
            // Normalisasi ke integer dan hanya ambil > 0
            $nums = array_values(array_filter(array_map(function($v){
                // handle string seperti ' 305 ' atau '00305'
                return (int) trim((string)$v);
            }, $raw), fn($n) => $n > 0));
            $used = array_fill_keys($nums, true);

            // Strategi: gunakan MEX (minimum excluded) agar mengisi celah yang kosong.
            // Jika client mengirim hint (nomor terbesar saat ini), targetkan hint+1 bila belum terpakai.
            if ($hint > 0 && !isset($used[$hint + 1])) {
                $next = (int) ($hint + 1);
            } else {
                $next = 1;
                while (isset($used[$next])) { $next++; }
            }

            $today = now();
            $po = PO::create([
                'no_invoice'  => (string) $next,
                'tanggal_po'  => $today->format('Y-m-d'),
                'no_po'       => '-',
                'no_surat_jalan' => '-',
                'produk_id'   => null,
                'qty'         => 0,
                'qty_jenis'   => 'PCS',
                'harga'       => 0,
                'total'       => 0,
                'customer'    => $customerNm ?: '-',
                'alamat_1'    => null,
                'alamat_2'    => null,
                'pengirim'    => null,
            ]);

            return response()->json([
                'success' => true,
                'id' => $po->id,
                'no_invoice' => $po->no_invoice,
                'tanggal' => $today->format('Y-m-d'),
                'tanggal_display' => $today->format('d/m/Y'),
                'customer' => $po->customer,
            ]);
        } catch (\Throwable $e) {
            \Log::error('invoiceQuickCreate gagal: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat draft invoice: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Set nomor urut invoice berikutnya: menyimpan acuan next dan membuat satu draft baris dengan nomor yang dipilih.
     */
    public function setNextInvoiceNumber(Request $request)
    {
        $request->validate([
            'next_number' => 'required|integer|min:1',
            'customer_id' => 'nullable|integer',
            'customer'    => 'nullable|string|max:255',
        ]);

        $nextNumber = (int) $request->input('next_number');
        $customerId = $request->input('customer_id');
        $customerNm = $request->input('customer');

        if (PO::where('no_invoice', (string) $nextNumber)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor urut ' . $nextNumber . ' sudah digunakan. Silakan pilih nomor lain.'
            ], 422);
        }

        // Catat referensi next di settings/session (best-effort)
        try { Setting::setValue('next_invoice_number', $nextNumber + 1); } catch (\Throwable $e) { session(['next_invoice_number_override' => $nextNumber + 1]); }

        // Buat draft baris agar langsung muncul di tabel
        $today = now();
        $po = PO::create([
            'no_invoice'  => (string) $nextNumber,
            'tanggal_po'  => $today->format('Y-m-d'),
            'no_po'       => '-',
            'no_surat_jalan' => '-',
            'produk_id'   => null,
            'qty'         => 0,
            'qty_jenis'   => 'PCS',
            'harga'       => 0,
            'total'       => 0,
            'customer'    => $customerNm ?: '-',
            'alamat_1'    => null,
            'alamat_2'    => null,
            'pengirim'    => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Nomor urut di-set ke ' . $nextNumber,
            'id' => $po->id,
            'no_invoice' => $po->no_invoice,
            'tanggal' => $today->format('Y-m-d'),
            'tanggal_display' => $today->format('d/m/Y'),
            'customer' => $po->customer,
        ]);
    }

    /**
     * Update invoice info (customer, tanggal invoice) via modal
     */
    public function updateInvoice(UpdateInvoiceRequest $request, PO $po)
    {
        // CEK: Jika ada Jatuh Tempo dengan status Lunas (Accept), BLOKIR update invoice
        if ($this->poService->isInvoiceLunas($po->no_invoice)) {
            return redirect()->back()->with('error', '❌ Invoice tidak dapat diubah karena Jatuh Tempo sudah berstatus LUNAS (Accept).');
        }

        try {
            $oldInvoice = (string) $po->no_invoice;
            $newInvoice = (string) $request->input('no_invoice');
            $newDate    = (string) $request->input('tanggal_invoice');

            // Jika nomor invoice berubah, pastikan belum dipakai grup lain
            if ($newInvoice !== $oldInvoice) {
                $exists = PO::where('no_invoice', $newInvoice)->exists();
                if ($exists) {
                    return redirect()->back()->with('error', 'No Invoice ' . $newInvoice . ' sudah digunakan. Silakan pilih nomor lain.');
                }
            }

            DB::transaction(function () use ($oldInvoice, $newInvoice, $newDate) {
                // Update semua baris dalam grup invoice lama
                PO::where('no_invoice', $oldInvoice)->update([
                    'no_invoice' => $newInvoice,
                    'tanggal_po' => $newDate,
                ]);

                // Update/relokasi entri JatuhTempo ke nomor baru (jika ada)
                $existingJT = JatuhTempo::where('no_invoice', $oldInvoice)->first();
                if ($existingJT) {
                    // Recalculate due date: +1 bulan (tanpa ketergantungan customer di sini)
                    $tanggalInvoice = \Carbon\Carbon::parse($newDate);
                    $tanggalJatuhTempo = (clone $tanggalInvoice)->addMonth();
                    $existingJT->update([
                        'no_invoice' => $newInvoice,
                        'tanggal_invoice' => $tanggalInvoice->format('Y-m-d'),
                        'tanggal_jatuh_tempo' => $tanggalJatuhTempo->format('Y-m-d'),
                    ]);
                }
            });

            return redirect()->route('invoice.index')->with('success', 'Invoice berhasil diupdate!');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal update invoice: ' . $e->getMessage());
        }
    }

}
