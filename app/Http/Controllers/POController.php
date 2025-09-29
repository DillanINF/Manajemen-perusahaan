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
use App\Models\Invoice; // Hapus entri invoice saat group delete
use App\Models\Setting;
use App\Models\SisaPOItem;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use PhpOffice\PhpSpreadsheet\IOFactory;

class POController extends Controller
{
    public function index()
    {
        // Ambil semua PO beserta relasi items->produk (relasi kendaraan tidak dipakai lagi)
        $pos        = PO::with(['items.produk'])->latest()->get();
        $produks    = Produk::all();
        $customers  = Customer::all();
        
        // CHANGE: Ambil data pengirim dari tabel pengirim beserta kendaraan & no_polisi untuk autofill
        $pengirims = Pengirim::select('nama','kendaraan','no_polisi')
            ->whereNotNull('nama')
            ->where('nama', '!=', '')
            ->orderBy('nama', 'asc')
            ->get();

        return view('dashboard.po_index', compact('pos', 'produks', 'customers', 'pengirims'));
    }

    public function create()
    {
        // Guard: Form Create PO hanya boleh diakses dari Data Invoice (double click)
        $from = request('from');
        $poNumber = request('po_number');
        if ($from !== 'invoice' || empty($poNumber)) {
            return redirect()->route('invoice.index')
                ->with('error', 'Akses formulir PO hanya melalui Data Invoice (double click pada nomor urut).');
        }
        $produks    = Produk::all();
        $customers  = Customer::all();
        
        // CHANGE: Ambil data pengirim beserta kendaraan & no_polisi
        $pengirims = Pengirim::select('nama', 'kendaraan', 'no_polisi')
            ->whereNotNull('nama')
            ->where('nama', '!=', '')
            ->orderBy('nama', 'asc')
            ->get();

        // Optional: tampilkan juga daftar PO seperti di index agar view konsisten
        $pos = PO::with(['items.produk'])->latest()->get();

        // Prefill tanggal dari draft POS (no urut terkait) atau fallback ke hari ini
        $prefillTanggal = null;
        $draft = PO::where('po_number', (int)$poNumber)->orderByDesc('id')->first();
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
        return view('dashboard.po_index', compact('pos', 'produks', 'customers', 'pengirims', 'prefillTanggal', 'po', 'sjCodeParts', 'sjNomor', 'sjPt', 'sjTahun'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'po_number'           => 'nullable|integer|min:1',
            'no_surat_jalan_nomor' => 'required|string',
            'no_surat_jalan_pt'    => 'required|string',
            'no_surat_jalan_tahun' => 'required|integer',
            'no_po'                => 'required|string',
            'no_invoice_nomor'     => 'nullable|string|max:255',
            'no_invoice_pt'        => 'nullable|string|max:255',
            'no_invoice_tanggal'   => 'nullable|integer|min:1|max:12',
            'no_invoice_tahun'     => 'nullable|integer',
            'customer_id'          => 'required|exists:customers,id',
            'tanggal_po'           => 'required|date',
            'kendaraan'            => 'nullable|string',
            'no_polisi'            => 'nullable|string',
            'address_1'            => 'required|string|max:255',
            'address_2'            => 'nullable|string|max:255',
            'pengirim'             => 'nullable|string|max:255',
            'items'                => 'required|array|min:1',
            'items.*.produk_id'    => 'required|exists:produks,id',
            'items.*.qty'          => 'required|integer|min:1',
            'items.*.qty_jenis'    => 'required|in:PCS,SET',
            'items.*.harga'        => 'nullable|integer|min:0',
            'items.*.total'        => 'nullable|integer|min:0',
        ]);

        // Gabungkan nomor surat jalan
        $noSuratJalan = "{$data['no_surat_jalan_nomor']}/{$data['no_surat_jalan_pt']}/{$data['no_surat_jalan_tahun']}";

        // Ambil nama customer dari customer_id
        $customer = Customer::find($data['customer_id']);
        $customerName = $customer ? $customer->name : '';

        if (empty($data['address_1']) || $data['address_1'] === '-') {
            $data['address_1'] = $customer->address_1 ?? '';
        }
        if (empty($data['address_2']) || $data['address_2'] === '-') {
            $data['address_2'] = $customer->address_2 ?? '';
        }

        // Bentuk no_invoice jika ada inputnya: NOMOR/PT/BULAN/TAHUN (bulan di kiri tahun)
        $noInvoice = null;
        if (!empty($data['no_invoice_nomor']) || !empty($data['no_invoice_pt']) || !empty($data['no_invoice_tanggal']) || !empty($data['no_invoice_tahun'])) {
            $nom = $data['no_invoice_nomor'] ?? '';
            $pt  = $data['no_invoice_pt'] ?? '';
            $bln = $data['no_invoice_tanggal'] ?? '';
            $thn = $data['no_invoice_tahun'] ?? '';
            $parts = array_filter([trim($nom), trim($pt), trim((string)$bln), trim((string)$thn)], fn($v) => $v !== '');
            $noInvoice = implode('/', $parts);
        }

        // Simpan ke database PO (header) + items dalam transaksi
        $po = DB::transaction(function () use ($data, $customerName, $noSuratJalan, $noInvoice, $customer) {
            $rawItems = collect($data['items'])
                ->filter(fn ($it) => !empty($it['produk_id']) && !empty($it['qty']))
                ->values();

            // Recompute harga & total server-side for each item
            $items = $rawItems->map(function ($it) {
                $produk = Produk::find($it['produk_id']);
                $qtyJenis = strtoupper($it['qty_jenis'] ?? 'PCS');
                $harga = 0;
                if ($produk) {
                    $harga = $qtyJenis === 'SET' ? (int) ($produk->harga_set ?? 0) : (int) ($produk->harga_pcs ?? 0);
                }
                $qty = (int) ($it['qty'] ?? 0);
                $total = $harga * $qty;
                return [
                    'produk_id' => (int) $it['produk_id'],
                    'qty'       => $qty,
                    'qty_jenis' => $qtyJenis,
                    'harga'     => $harga,
                    'total'     => $total,
                ];
            });

            if ($items->isEmpty()) {
                throw ValidationException::withMessages([
                    'items' => 'Minimal 1 item dengan produk dan qty >= 1.',
                ]);
            }

            // Auto-split PO: cek stok dan buat PO dengan qty tersedia, simpan sisa ke tabel khusus
            $qtyByProduk = [];
            foreach ($items as $it) {
                $qtyByProduk[$it['produk_id']] = ($qtyByProduk[$it['produk_id']] ?? 0) + (int) $it['qty'];
            }

            $splitMessages = [];
            $adjustedItems = collect();
            $sisaItems = [];

            if (!empty($qtyByProduk)) {
                $produkMap = Produk::query()
                    ->whereIn('id', array_keys($qtyByProduk))
                    ->withSum('barangMasuks as qty_masuk', 'qty')
                    ->withSum('barangKeluars as qty_keluar', 'qty')
                    ->get()
                    ->keyBy('id');

                // Untuk stok 0: izinkan input tapi semua masuk ke Sisa Data PO
                // Tidak ada validasi error untuk stok 0, langsung proses split

                // Track stok yang sudah dialokasikan per produk dalam batch ini
                $stokDialokasikan = [];

                foreach ($items as $it) {
                    $pid = $it['produk_id'];
                    $qtyReq = (int) $it['qty'];
                    $p = $produkMap->get($pid);
                    $stokTotal = (int) (($p->qty_masuk ?? 0) - ($p->qty_keluar ?? 0));
                    
                    // Kurangi stok yang sudah dialokasikan untuk produk yang sama dalam batch ini
                    $stokTersedia = $stokTotal - ($stokDialokasikan[$pid] ?? 0);
                    $stokTersedia = max(0, $stokTersedia); // Pastikan tidak negatif

                    // LOGIKA BARU: Cek stok tersedia dulu
                    if ($stokTersedia <= 0) {
                        // STOK 0: Seluruh qty masuk ke Sisa Data PO, TIDAK ADA yang ke Surat Jalan
                        $sisaItems[] = [
                            'no_po' => $data['no_po'],
                            'produk_id' => $pid,
                            'qty_diminta' => $qtyReq,
                            'qty_tersedia' => 0,
                            'qty_sisa' => $qtyReq, // Seluruh qty jadi sisa
                            'qty_jenis' => $it['qty_jenis'],
                            'harga' => (float) $it['harga'],
                            'total_sisa' => $qtyReq * (float) $it['harga'],
                            'customer' => $customerName,
                            'tanggal_po' => $data['tanggal_po'],
                            'status' => 'pending',
                            'keterangan' => 'Stok habis - seluruh pesanan masuk ke Sisa Data PO'
                        ];
                        
                        $splitMessages[] = 'Produk "' . ($p->nama_produk ?? ('ID '.$pid)) . '": Stok habis (0), seluruh pesanan ' . $qtyReq . ' pcs masuk ke Sisa Data PO.';
                        
                    } else if ($qtyReq > $stokTersedia) {
                        // STOK TIDAK CUKUP: Sebagian ke Surat Jalan, sebagian ke Sisa Data PO
                        // Yang tersedia masuk ke Surat Jalan
                        $adjustedItem = $it;
                        $adjustedItem['qty'] = $stokTersedia;
                        $adjustedItem['total'] = $stokTersedia * (float) $it['harga'];
                        $adjustedItems->push($adjustedItem);
                        
                        // Update stok yang sudah dialokasikan
                        $stokDialokasikan[$pid] = ($stokDialokasikan[$pid] ?? 0) + $stokTersedia;

                        // Sisa masuk ke Sisa Data PO
                        $qtySisa = $qtyReq - $stokTersedia;
                        $sisaItems[] = [
                            'no_po' => $data['no_po'],
                            'produk_id' => $pid,
                            'qty_diminta' => $qtyReq,
                            'qty_tersedia' => $stokTersedia,
                            'qty_sisa' => $qtySisa,
                            'qty_jenis' => $it['qty_jenis'],
                            'harga' => (float) $it['harga'],
                            'total_sisa' => $qtySisa * (float) $it['harga'],
                            'customer' => $customerName,
                            'tanggal_po' => $data['tanggal_po'],
                            'status' => 'pending',
                            'keterangan' => 'Auto-split dari PO karena stok tidak mencukupi'
                        ];
                        
                        $splitMessages[] = 'Produk "' . ($p->nama_produk ?? ('ID '.$pid)) . '": Diminta ' . $qtyReq . ', tersedia ' . $stokTersedia . ', sisa ' . $qtySisa . ' pcs masuk ke Sisa Data PO.';
                    } else {
                        // Stok cukup, tambahkan item normal
                        $adjustedItems->push($it);
                        // Update stok yang sudah dialokasikan
                        $stokDialokasikan[$pid] = ($stokDialokasikan[$pid] ?? 0) + $qtyReq;
                    }
                }

                // Update items dengan yang sudah disesuaikan
                $items = $adjustedItems;
            }

            // Header mengambil item pertama untuk kolom legacy
            $first = $items->first();
            $sumTotal = (int) $items->sum(fn ($it) => (int) ($it['total'] ?? 0));

            // Jika tidak ada item yang masuk ke PO (semua stok 0), JANGAN buat PO record
            // Hanya simpan ke Sisa Data PO saja
            if ($items->isEmpty()) {
                // Simpan sisa items ke tabel sisa_po_items jika ada
                if (!empty($sisaItems)) {
                    foreach ($sisaItems as $sisaItem) {
                        SisaPOItem::create($sisaItem);
                    }
                }

                // Simpan pesan split ke session untuk ditampilkan sebagai notifikasi
                if (!empty($splitMessages)) {
                    session()->flash('split_messages', $splitMessages);
                }

                // Return dari dalam transaction
                return null; // Akan dihandle di luar transaction
            }

            // Cari draft berdasarkan po_number
            $po = null;
            $shouldUpdateDraft = false;
            if (!empty($data['po_number'])) {
                $po = PO::where('po_number', (int)$data['po_number'])->orderByDesc('id')->first();
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
                    'po_number'     => $data['po_number'], // PERBAIKAN: Pastikan po_number tetap sama
                    'tanggal_po'    => $data['tanggal_po'],
                    'customer_id'   => $data['customer_id'],
                    'customer'      => $customerName,
                    'no_surat_jalan'=> $noSuratJalan,
                    'no_po'         => $data['no_po'],
                    'no_invoice'     => $noInvoice,
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

                // Replace items lama (seharusnya kosong; jaga-jaga)
                $po->items()->delete();
                foreach ($items as $it) {
                    POItem::create([
                        'po_id'     => $po->id,
                        'produk_id' => $it['produk_id'],
                        'qty'       => $it['qty'],
                        'qty_jenis' => $it['qty_jenis'],
                        'harga'     => $it['harga'],
                        'total'     => $it['total'],
                    ]);
                }
                // Catat Barang Keluar otomatis (hanya jika ada items yang masuk ke PO)
                if (!$items->isEmpty()) {
                    foreach ($items as $it) {
                        // Hanya catat barang keluar jika qty > 0
                        if ((int) $it['qty'] > 0) {
                            BarangKeluar::create([
                                'produk_id' => $it['produk_id'],
                                'qty'       => (int) $it['qty'],
                                'tanggal'   => $data['tanggal_po'],
                                'keterangan'=> 'Auto Keluar dari PO ' . ($data['no_po'] ?? ''),
                                'user_id'   => auth()->id(),
                            ]);
                        }
                    }
                }
            } else {
                // CREATE baris baru meskipun po_number sama (mendukung input >1x untuk No Urut yang sama)
                $po = PO::create([
                    'po_number'     => $data['po_number'] ?? null,
                    'tanggal_po'    => $data['tanggal_po'],
                    'customer_id'   => $data['customer_id'],
                    'customer'      => $customerName,
                    'no_surat_jalan'=> $noSuratJalan,
                    'no_po'         => $data['no_po'],
                    'no_invoice'     => $noInvoice,
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

                foreach ($items as $it) {
                    POItem::create([
                        'po_id'     => $po->id,
                        'produk_id' => $it['produk_id'],
                        'qty'       => $it['qty'],
                        'qty_jenis' => $it['qty_jenis'],
                        'harga'     => $it['harga'],
                        'total'     => $it['total'],
                    ]);
                }
                // Catat Barang Keluar otomatis (hanya jika ada items yang masuk ke PO)
                if (!$items->isEmpty()) {
                    foreach ($items as $it) {
                        // Hanya catat barang keluar jika qty > 0
                        if ((int) $it['qty'] > 0) {
                            BarangKeluar::create([
                                'produk_id' => $it['produk_id'],
                                'qty'       => (int) $it['qty'],
                                'tanggal'   => $data['tanggal_po'],
                                'keterangan'=> 'Auto Keluar dari PO ' . ($data['no_po'] ?? ''),
                                'user_id'   => auth()->id(),
                            ]);
                        }
                    }
                }
            }

            // Simpan sisa items ke tabel sisa_po_items jika ada
            if (!empty($sisaItems)) {
                foreach ($sisaItems as $sisaItem) {
                    SisaPOItem::create($sisaItem);
                }
            }

            return $po;
        });

        // Simpan pesan split ke session untuk ditampilkan sebagai notifikasi
        if (!empty($splitMessages)) {
            session()->flash('split_messages', $splitMessages);
        }

        // Bersihkan reserved jika nomor ini ada di cache (nomor sudah resmi tersimpan)
        if (!empty($data['po_number'])) {
            $reserved = (array) (session('invoice_reserved_numbers', []));
            $reserved = array_values(array_filter($reserved, fn($v) => (int)$v !== (int)$data['po_number']));
            session(['invoice_reserved_numbers' => $reserved]);
            // Jika epoch aktif, catat nomor ini sebagai saved dalam epoch
            if ((bool) session('invoice_epoch_active', false)) {
                $epochSaved = (array) (session('invoice_epoch_saved_numbers', []));
                $epochSaved[] = (int) $data['po_number'];
                $epochSaved = array_values(array_slice(array_unique(array_map('intval', $epochSaved)), -500));
                session(['invoice_epoch_saved_numbers' => $epochSaved]);
            }
        }

        // Handle kasus khusus jika semua produk stok 0
        if ($po === null) {
            // Redirect kembali ke form dengan pesan bahwa semua masuk ke Sisa Data PO
            $from = $request->input('from', 'invoice');
            $poNumber = $request->input('po_number');
            return redirect()
                ->route('po.create', [
                    'from' => $from,
                    'po_number' => $poNumber,
                    'tanggal_po' => $data['tanggal_po'],
                ])
                ->with('success', 'Data PO disimpan! Semua produk masuk ke Sisa Data PO karena stok habis.');
        }

        // === Ekspor ke Excel ===
        $templatePath = storage_path('app/template/SJ CAM & OUTSTANDING 2024.xlsm');
        if (file_exists($templatePath)) {
            $spreadsheet  = IOFactory::load($templatePath);
            $sheet        = $spreadsheet->getActiveSheet();
            $row = 10; // baris awal tulis data
            foreach ($po->items()->with('produk')->get() as $item) {
                $sheet->setCellValue("A{$row}", $noSuratJalan);
                $sheet->setCellValue("B{$row}", $po->no_po);
                $sheet->setCellValue("C{$row}", $po->customer);
                $sheet->setCellValue("D{$row}", date('d/m/Y', strtotime($po->tanggal_po)));
                $sheet->setCellValue("E{$row}", $item->produk->nama_produk ?? 'Produk Tidak Ditemukan');
                $sheet->setCellValue("F{$row}", $item->qty . ' ' . $item->qty_jenis);
                $sheet->setCellValue("G{$row}", $item->harga);
                $sheet->setCellValue("H{$row}", $item->total);
                $sheet->setCellValue("I{$row}", ($po->kendaraan ?? '-') . ' / ' . ($po->no_polisi ?? '-'));
                $sheet->setCellValue("J{$row}", ($po->alamat_1 ?? '-') . ' ' . ($po->alamat_2 ?? ''));
                $row++;
            }

            $filename   = 'PO_' . now()->format('Ymd_His') . '.xlsm';
            $exportPath = storage_path('app/public/exports/');

            if (!file_exists($exportPath)) {
                mkdir($exportPath, 0777, true);
            }

            $savePath = $exportPath . $filename;
            IOFactory::createWriter($spreadsheet, 'Xls')->save($savePath);
        }

        // Handle kasus khusus jika semua produk stok 0
        if ($po === null) {
            // Redirect kembali ke form dengan pesan bahwa semua masuk ke Sisa Data PO
            $from = $request->input('from', 'invoice');
            $poNumber = $request->input('po_number');
            return redirect()
                ->route('po.create', [
                    'from' => $from,
                    'po_number' => $poNumber,
                    'tanggal_po' => $data['tanggal_po'],
                ])
                ->with('success', 'Data PO disimpan! Semua produk masuk ke Sisa Data PO karena stok habis.');
        }

        // === Sinkronisasi ke Jatuh Tempo ===
        // HANYA buat Jatuh Tempo jika status_approval Data PO adalah 'Accept'
        if (($po->status_approval ?? 'Pending') === 'Accept') {
            try {
                // Gunakan No Invoice asli jika ada; jika kosong, fallback ke No Urut Invoice (po_number) dari Form Data Invoice
                // JANGAN fallback ke No Surat Jalan
                // Gunakan po_number (no urut invoice dari Data Invoice) sebagai no_invoice di Jatuh Tempo
                $invoiceKey = $data['po_number'] ?? $po->no_invoice;
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
                    'jumlah_tagihan' => (int) ($po->total ?? 0),
                    'jumlah_terbayar' => 0,
                    'sisa_tagihan' => (int) ($po->total ?? 0),
                    'status_pembayaran' => 'Belum Bayar',
                    'status_approval' => 'Pending',
                ];

                if ($existingJT) {
                    // UPDATE JATUH TEMPO YANG SUDAH ADA
                    $existingJT->update($jtPayload);
                    \Log::info('[JT] Updated existing JatuhTempo for Accept status', [
                        'jt_id' => $existingJT->id,
                        'po_id' => $po->id,
                        'payload' => $jtPayload
                    ]);
                } else {
                    // BUAT JATUH TEMPO BARU
                    $jt = JatuhTempo::create($jtPayload);
                    \Log::info('[JT] Created new JatuhTempo for Accept status', [
                        'jt_id' => $jt->id,
                        'po_id' => $po->id,
                        'payload' => $jtPayload
                    ]);
                }
            } catch (\Throwable $e) {
                \Log::warning('[JT] Sync JatuhTempo gagal', [
                    'error' => $e->getMessage(),
                    'code' => method_exists($e, 'getCode') ? $e->getCode() : null,
                ]);
            }
        }

        // Selalu tetap berada di Form Input PO setelah simpan
        $from = $request->input('from', 'invoice');
        $poNumber = $request->input('po_number') ?? $po->po_number;
        return redirect()
            ->route('po.create', [
                'from' => $from ?: 'invoice',
                'po_number' => $poNumber,
                'tanggal_po' => \Carbon\Carbon::parse($po->tanggal_po)->format('Y-m-d'),
            ])
            ->with('success', 'Data PO berhasil disimpan! Tetap di Form Input PO untuk melanjutkan.');
    }

    /**
     * Toggle status approval PO (Pending <-> Accept) dan sinkronisasi Jatuh Tempo.
     */
    public function toggleStatus(Request $request, PO $po)
    {
        $invoiceKey = $po->po_number ?? $po->no_invoice;
        // Ambil semua PO dalam grup invoice yang sama
        $groupPos = PO::where('po_number', $po->po_number)->get();
        $allAccept = $groupPos->every(function ($p) {
            return (($p->status_approval ?? 'Pending') === 'Accept');
        });
        // Jika semua sudah Accept, toggle ke Pending; jika ada yang Pending, set semua ke Accept
        $new = $allAccept ? 'Pending' : 'Accept';

        DB::transaction(function () use ($po, $groupPos, $new, $invoiceKey) {
            // Update semua PO dalam grup
            PO::where('po_number', $po->po_number)->update(['status_approval' => $new]);

            if ($new === 'Accept') {
                try {
                    $customer = Customer::find($po->customer_id);
                    $tanggalInvoice = \Carbon\Carbon::parse($po->tanggal_po);
                    $termsDays = (int) (($customer->payment_terms_days ?? 0));
                    $tanggalJatuhTempo = $termsDays > 0
                        ? (clone $tanggalInvoice)->addDays($termsDays)
                        : (clone $tanggalInvoice)->addMonth();

                    // Buat satu baris Jatuh Tempo per PO dalam grup
                    $groupPosRefreshed = PO::where('po_number', $po->po_number)->get();
                    foreach ($groupPosRefreshed as $row) {
                        $payload = [
                            'no_invoice' => $invoiceKey,
                            'no_po' => $row->no_po,
                            'customer' => $row->customer,
                            'tanggal_invoice' => $tanggalInvoice->format('Y-m-d'),
                            'tanggal_jatuh_tempo' => $tanggalJatuhTempo->format('Y-m-d'),
                            'jumlah_tagihan' => (int) ($row->total ?? 0),
                            'jumlah_terbayar' => 0,
                            'sisa_tagihan' => (int) ($row->total ?? 0),
                            'status_pembayaran' => 'Belum Bayar',
                            'status_approval' => 'Pending',
                        ];

                        \App\Models\JatuhTempo::updateOrCreate(
                            ['no_invoice' => $invoiceKey, 'no_po' => $row->no_po],
                            $payload
                        );
                    }
                } catch (\Throwable $e) {
                    \Log::warning('[JT] Toggle group sync failed', ['error' => $e->getMessage()]);
                }
            } else {
                // Kembalikan ke Pending: hapus semua JT untuk no_invoice ini
                try {
                    \App\Models\JatuhTempo::where('no_invoice', $invoiceKey)->delete();
                } catch (\Throwable $e) {
                    \Log::warning('[JT] Delete JT on group toggle back failed', ['error' => $e->getMessage()]);
                }
            }
        });

        $response = [
            'success' => true,
            'status' => $new,
            'message' => $new === 'Accept' ? 'Semua PO dalam invoice ini di-Set ke Accept dan disinkronkan (1 baris) ke Jatuh Tempo.' : 'Semua PO dalam invoice ini dikembalikan ke Pending dan Jatuh Tempo dihapus.',
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

        return view('dashboard.po_index', compact('po', 'pos', 'produks', 'customers', 'pengirims'));
    }

    public function update(Request $request, PO $po)
    {
        $data = $request->validate([
            'po_number'           => 'nullable|integer|min:1',
            'no_surat_jalan_nomor' => 'required|string',
            'no_surat_jalan_pt'    => 'required|string',
            'no_surat_jalan_tahun' => 'required|integer',
            'no_po'                => 'required|string',
            'no_invoice_nomor'     => 'nullable|string|max:255',
            'no_invoice_pt'        => 'nullable|string|max:255',
            'no_invoice_tahun'     => 'nullable|integer',
            'customer_id'          => 'required|exists:customers,id',
            'tanggal_po'           => 'required|date',
            'kendaraan'            => 'nullable|string',
            'no_polisi'            => 'nullable|string',
            'address_1'            => 'required|string|max:255',
            'address_2'            => 'nullable|string|max:255',
            'pengirim'             => 'nullable|string|max:255',
            'items'                => 'required|array|min:1',
            'items.*.produk_id'    => 'required|exists:produks,id',
            'items.*.qty'          => 'required|integer|min:1',
            'items.*.qty_jenis'    => 'required|in:PCS,SET',
            'items.*.harga'        => 'nullable|integer|min:0',
            'items.*.total'        => 'nullable|integer|min:0',
        ]);

        // Gabungkan nomor surat jalan
        $noSuratJalan = "{$data['no_surat_jalan_nomor']}/{$data['no_surat_jalan_pt']}/{$data['no_surat_jalan_tahun']}";

        // Ambil nama customer dari customer_id
        $customer = Customer::find($data['customer_id']);
        $customerName = $customer ? $customer->name : '';

        if (empty($data['address_1']) || $data['address_1'] === '-') {
            $data['address_1'] = $customer->address_1 ?? '';
        }
        if (empty($data['address_2']) || $data['address_2'] === '-') {
            $data['address_2'] = $customer->address_2 ?? '';
        }

        // Bentuk no_invoice jika ada inputnya
        $noInvoice = null;
        if (!empty($data['no_invoice_nomor']) || !empty($data['no_invoice_pt']) || !empty($data['no_invoice_tahun'])) {
            $nom = $data['no_invoice_nomor'] ?? '';
            $pt  = $data['no_invoice_pt'] ?? '';
            $thn = $data['no_invoice_tahun'] ?? '';
            $noInvoice = trim($nom) . '/' . trim($pt) . '/' . trim((string) $thn);
        }

        DB::transaction(function () use ($po, $data, $customerName, $noSuratJalan, $noInvoice) {
            $rawItems = collect($data['items'])
                ->filter(fn ($it) => !empty($it['produk_id']) && !empty($it['qty']))
                ->values();

            // Recompute harga & total server-side
            $items = $rawItems->map(function ($it) {
                $produk = Produk::find($it['produk_id']);
                $qtyJenis = strtoupper($it['qty_jenis'] ?? 'PCS');
                $harga = 0;
                if ($produk) {
                    $harga = $qtyJenis === 'SET' ? (int) ($produk->harga_set ?? 0) : (int) ($produk->harga_pcs ?? 0);
                }
                $qty = (int) ($it['qty'] ?? 0);
                $total = $harga * $qty;
                return [
                    'produk_id' => (int) $it['produk_id'],
                    'qty'       => $qty,
                    'qty_jenis' => $qtyJenis,
                    'harga'     => $harga,
                    'total'     => $total,
                ];
            });

            // Validasi stok cukup per produk (gabungkan qty per produk dulu)
            $qtyByProduk = [];
            foreach ($items as $it) {
                $qtyByProduk[$it['produk_id']] = ($qtyByProduk[$it['produk_id']] ?? 0) + (int) $it['qty'];
            }
            if (!empty($qtyByProduk)) {
                $produkMap = Produk::query()
                    ->whereIn('id', array_keys($qtyByProduk))
                    ->withSum('barangMasuks as qty_masuk', 'qty')
                    ->withSum('barangKeluars as qty_keluar', 'qty')
                    ->get()
                    ->keyBy('id');
                $errors = [];
                foreach ($qtyByProduk as $pid => $qtyReq) {
                    $p = $produkMap->get($pid);
                    $sisa = (int) (($p->qty_masuk ?? 0) - ($p->qty_keluar ?? 0));
                    
                    // Validasi stok 0 - tidak boleh input produk dengan stok habis
                    if ($sisa <= 0) {
                        $errors[] = 'Produk "' . ($p->nama_produk ?? ('ID '.$pid)) . '" memiliki stok 0. Tidak dapat diinput ke PO.';
                    } elseif ($qtyReq > $sisa) {
                        $errors[] = 'Stok untuk "' . ($p->nama_produk ?? ('ID '.$pid)) . '" kurang. Sisa: ' . $sisa . ', diminta: ' . $qtyReq . '.';
                    }
                }
                if (!empty($errors)) {
                    throw ValidationException::withMessages(['items' => implode(' ', $errors)]);
                }
            }

            $first = $items->first();
            $sumTotal = (int) $items->sum(fn ($it) => (int) ($it['total'] ?? 0));

            // Pastikan nomor urut tetap ada: jika kosong di DB namun request membawa po_number, set sekali.
            $updatePayload = [
                // Jangan ubah po_number saat update agar nomor urut tetap konsisten (kecuali fallback di bawah)
                'no_surat_jalan' => $noSuratJalan,
                'no_po'          => $data['no_po'],
                'no_invoice'     => $noInvoice,
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
            if (empty($po->po_number) && !empty($data['po_number'])) {
                $updatePayload['po_number'] = (int)$data['po_number'];
            }

            $po->update($updatePayload);

            // Replace all items
            $po->items()->delete();
            foreach ($items as $it) {
                POItem::create([
                    'po_id'     => $po->id,
                    'produk_id' => $it['produk_id'],
                    'qty'       => $it['qty'],
                    'qty_jenis' => $it['qty_jenis'],
                    'harga'     => $it['harga'],
                    'total'     => $it['total'],
                ]);
            }

            // Refresh Barang Keluar otomatis untuk PO ini (hapus yang lama berdasarkan keterangan standar)
            try {
                // Hapus berdasarkan no_po lama maupun no_po baru (jika user mengubah no_po)
                $oldNoPo = $po->getOriginal('no_po') ?: $po->no_po;
                $newNoPo = $data['no_po'] ?? $po->no_po;
                \App\Models\BarangKeluar::whereIn('keterangan', [
                    'Auto Keluar dari PO ' . (string) $oldNoPo,
                    'Auto Keluar dari PO ' . (string) $newNoPo,
                ])->delete();
            } catch (\Throwable $e) { /* ignore */ }
            foreach ($items as $it) {
                BarangKeluar::create([
                    'produk_id' => $it['produk_id'],
                    'qty'       => (int) $it['qty'],
                    'tanggal'   => $data['tanggal_po'],
                    'keterangan'=> 'Auto Keluar dari PO ' . ($data['no_po'] ?? $po->no_po),
                    'user_id'   => auth()->id(),
                ]);
            }
        });

        // Bersihkan reserved jika nomor ini ada di cache (nomor sudah resmi tersimpan)
        if (!empty($data['po_number'])) {
            $reserved = (array) (\Cache::get('invoice_reserved_numbers', []));
            $reserved = array_values(array_filter($reserved, fn($v) => (int)$v !== (int)$data['po_number']));
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
        $poNumber = $request->input('po_number') ?? $po->po_number;
        return redirect()
            ->route('po.create', [
                'from' => $from,
                'po_number' => $poNumber,
                'tanggal_po' => \Carbon\Carbon::parse($po->tanggal_po)->format('Y-m-d'),
            ])
            ->with('success', 'Data PO berhasil disimpan! 🎉');
    }

    public function destroy(PO $po)
    {
        // Lepas nomor dari reserved cache & epoch agar bisa digunakan lagi
        $num = (int) ($po->po_number ?? 0);

        // HAPUS SELURUH GRUP (Data Invoice) SEKALI KLIK dari halaman Data Invoice
        $from = request('from');
        $referer = request()->headers->get('referer');
        $isInvoiceContext = ($from === 'invoice') || ($referer && str_contains($referer, '/po/invoices'));
        $isGroup = request()->boolean('group');
        if ($isInvoiceContext && !empty($po->po_number)) {
            DB::transaction(function() use ($po, $num) {
                $group = PO::where('po_number', $po->po_number)->get();

                // Kumpulkan semua no_po valid untuk penghapusan JatuhTempo berdasarkan no_po
                $noPoList = $group->pluck('no_po')->filter(function($v){
                    $t = trim((string)$v);
                    return $t !== '' && $t !== '-';
                })->values();

                foreach ($group as $gp) {
                    // Rollback stok HANYA jika belum Accept
                    try {
                        $isAccept = (string)($gp->status_approval ?? 'Pending') === 'Accept';
                        if (!$isAccept && !empty($gp->no_po)) {
                            $noPoTrim = trim((string)$gp->no_po);
                            $items = $gp->items()->get(['produk_id']);
                            foreach ($items as $it) {
                                \App\Models\BarangKeluar::where('produk_id', $it->produk_id)
                                    ->where(function($q) use ($noPoTrim, $gp) {
                                        $exact = 'Auto Keluar dari PO ' . (string) $gp->no_po;
                                        $exactTrim = 'Auto Keluar dari PO ' . $noPoTrim;
                                        $q->where('keterangan', $exact)
                                          ->orWhere('keterangan', $exactTrim)
                                          ->orWhereRaw('TRIM(keterangan) = ?', [trim($exact)])
                                          ->orWhereRaw('TRIM(keterangan) = ?', [trim($exactTrim)])
                                          ->orWhere('keterangan', 'LIKE', '%PO ' . $noPoTrim . '%');
                                    })
                                    ->delete();
                            }
                        }
                    } catch (\Throwable $e) { /* ignore */ }

                    // Hapus entitas PO
                    $gp->delete();
                }

                // Hapus JatuhTempo berdasarkan no_invoice (po_number) dan daftar no_po dalam grup
                try {
                    $noInvKey = trim((string)$po->po_number);
                    if ($noInvKey !== '') {
                        // Hapus baris pada tabel invoices yang memiliki no_invoice sama
                        Invoice::where('no_invoice', $noInvKey)->delete();
                        Invoice::whereRaw('TRIM(no_invoice) = ?', [$noInvKey])->delete();
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
                if ($num > 0) {
                    $reserved = (array) (session('invoice_reserved_numbers', []));
                    $reserved = array_values(array_filter($reserved, fn($v) => (int)$v !== $num));
                    session(['invoice_reserved_numbers' => $reserved]);
                    if ((bool) session('invoice_epoch_active', false)) {
                        $epochSaved = (array) (session('invoice_epoch_saved_numbers', []));
                        $epochSaved = array_values(array_filter($epochSaved, fn($v) => (int)$v !== $num));
                        session(['invoice_epoch_saved_numbers' => $epochSaved]);
                    }
                }
            });

            return redirect()->route('invoice.index')->with('success', 'Seluruh baris untuk No Invoice ' . $num . ' berhasil dihapus.');
        }
        // Rollback stok (hapus BarangKeluar) HANYA jika status belum Accept
        try {
            $isAccept = (string)($po->status_approval ?? 'Pending') === 'Accept';
            if (!$isAccept && !empty($po->no_po)) {
                DB::transaction(function() use ($po) {
                    $noPoTrim = trim((string)$po->no_po);
                    $items = $po->items()->get(['produk_id']);
                    foreach ($items as $it) {
                        // Hapus per-produk untuk memastikan rollback akurat
                        \App\Models\BarangKeluar::where('produk_id', $it->produk_id)
                            ->where(function($q) use ($noPoTrim, $po) {
                                $exact = 'Auto Keluar dari PO ' . (string) $po->no_po;
                                $exactTrim = 'Auto Keluar dari PO ' . $noPoTrim;
                                $q->where('keterangan', $exact)
                                  ->orWhere('keterangan', $exactTrim)
                                  ->orWhereRaw('TRIM(keterangan) = ?', [trim($exact)])
                                  ->orWhereRaw('TRIM(keterangan) = ?', [trim($exactTrim)])
                                  ->orWhere('keterangan', 'LIKE', '%PO ' . $noPoTrim . '%');
                            })
                            ->delete();
                    }
                });
                \Log::info('[Stock] Rollback BK by items', ['no_po' => $po->no_po, 'po_id' => $po->id, 'items' => $po->items()->count()]);
            }
        } catch (\Throwable $e) { /* ignore */ }
        // Cascade delete: hapus JatuhTempo terkait no_invoice dan no_po
        try {
            $noInvKey = trim((string)($po->po_number ?? $po->no_invoice ?? ''));
            $noInvAlt = trim((string)($po->no_invoice ?? ''));

            if ($noInvKey !== '') {
                JatuhTempo::where('no_invoice', $noInvKey)->delete();
                JatuhTempo::whereRaw('TRIM(no_invoice) = ?', [$noInvKey])->delete();
                if (is_numeric($noInvKey)) {
                    $asInt = (string) ((int) $noInvKey);
                    JatuhTempo::where('no_invoice', $asInt)->delete();
                    JatuhTempo::whereRaw('TRIM(no_invoice) = ?', [$asInt])->delete();
                }
            }
            if ($noInvAlt !== '' && $noInvAlt !== $noInvKey) {
                JatuhTempo::where('no_invoice', $noInvAlt)->delete();
                JatuhTempo::whereRaw('TRIM(no_invoice) = ?', [$noInvAlt])->delete();
            }

            // Hapus juga berdasarkan semua no_po dalam grup nomor urut yang sama
            if (!empty($po->po_number)) {
                $groupNoPos = PO::where('po_number', $po->po_number)
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
                'po_number' => $po->po_number,
                'no_po' => $po->no_po,
            ]);
        }

        $po->delete();
        if ($num > 0) {
            $reserved = (array) (session('invoice_reserved_numbers', []));
            $reserved = array_values(array_filter($reserved, fn($v) => (int)$v !== $num));
            session(['invoice_reserved_numbers' => $reserved]);
            if ((bool) session('invoice_epoch_active', false)) {
                $epochSaved = (array) (session('invoice_epoch_saved_numbers', []));
                $epochSaved = array_values(array_filter($epochSaved, fn($v) => (int)$v !== $num));
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
            
            // Gunakan po_number sebagai no_invoice di Jatuh Tempo
            $invoiceKey = $po->po_number ?? $po->no_invoice;
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
        // Ambil daftar PO dan tampilkan no urut dari po_number
        $invoices = PO::query()
            ->latest('tanggal_po')
            ->get()
            ->map(function ($po) {
                // Saring nilai placeholder "Draft" agar tidak tampil di UI
                $barangRel = optional($po->produkRel)->nama_produk;
                $barang = $barangRel ?: ($po->produk ?? null);
                if (is_string($barang) && strtolower(trim($barang)) === 'draft') {
                    $barang = null;
                }
                $customer = $po->customer;
                if (is_string($customer) && strtolower(trim($customer)) === 'draft') {
                    $customer = '-';
                }

                return (object) [
                    'id'        => $po->id,
                    'tanggal'   => $po->tanggal_po ? \Carbon\Carbon::parse($po->tanggal_po)->format('d/m/Y') : '-',
                    'no_urut'   => $po->po_number ?? '-',
                    'customer'  => $customer,
                    'no_po'     => $po->no_po,
                    'barang'    => $barang,
                    'qty'       => $po->qty,
                    'harga'     => $po->harga,
                    'total'     => $po->total,
                    'status_approval' => $po->status_approval ?? 'Pending',
                ];
            });

        $customers = Customer::all();
        return view('dashboard.po_invoice_index', compact('invoices', 'customers'));
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
                    ->whereNotNull('po_number')
                    ->where('po_number', '>', 0)
                    ->pluck('po_number')
                    ->toArray();
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

            // Kumpulkan SEMUA nomor terpakai secara robust (tanpa filter > 0 di query)
            $raw = PO::query()
                ->whereNotNull('po_number')
                ->pluck('po_number')
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
                'po_number'   => $next,
                'tanggal_po'  => $today->format('Y-m-d'),
                'no_po'       => '-',
                'no_surat_jalan' => '-',
                'produk_id'   => null,
                'qty'         => 0,
                'qty_jenis'   => 'PCS',
                'harga'       => 0,
                'total'       => 0,
                'customer_id' => $customerId,
                'customer'    => $customerNm ?: '-',
                'alamat_1'    => null,
                'alamat_2'    => null,
                'pengirim'    => null,
            ]);

            return response()->json([
                'success' => true,
                'id' => $po->id,
                'po_number' => (int) $po->po_number,
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

        if (PO::where('po_number', $nextNumber)->exists()) {
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
            'po_number'   => $nextNumber,
            'tanggal_po'  => $today->format('Y-m-d'),
            'no_po'       => '-',
            'no_surat_jalan' => '-',
            'produk_id'   => null,
            'qty'         => 0,
            'qty_jenis'   => 'PCS',
            'harga'       => 0,
            'total'       => 0,
            'customer_id' => $customerId,
            'customer'    => $customerNm ?: '-',
            'alamat_1'    => null,
            'alamat_2'    => null,
            'pengirim'    => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Nomor urut di-set ke ' . $nextNumber,
            'id' => $po->id,
            'po_number' => (int) $po->po_number,
            'tanggal' => $today->format('Y-m-d'),
            'tanggal_display' => $today->format('d/m/Y'),
            'customer' => $po->customer,
        ]);
    }
}
