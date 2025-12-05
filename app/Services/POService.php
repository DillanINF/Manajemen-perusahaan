<?php

namespace App\Services;

use App\Models\PO;
use App\Models\POItem;
use App\Models\Produk;
use App\Models\Customer;
use App\Models\BarangKeluar;
use App\Models\SisaPOItem;
use App\Models\JatuhTempo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\IOFactory;

class POService
{
    /**
     * Process dan validasi items dari request
     */
    public function processItems(array $rawItems): array
    {
        $items = collect($rawItems)
            ->filter(fn ($it) => !empty($it['produk_id']) && !empty($it['qty']))
            ->map(function ($it) {
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
            })
            ->values()
            ->all();

        if (empty($items)) {
            throw ValidationException::withMessages([
                'items' => 'Minimal 1 item dengan produk dan qty >= 1.',
            ]);
        }

        return $items;
    }

    /**
     * Validasi stok dan auto-split items
     */
    public function validateAndSplitStock(array $items, array $data, string $customerName): array
    {
        $qtyByProduk = [];
        foreach ($items as $it) {
            $qtyByProduk[$it['produk_id']] = ($qtyByProduk[$it['produk_id']] ?? 0) + (int) $it['qty'];
        }

        $adjustedItems = collect();
        $sisaItems = [];
        $splitMessages = [];
        $hadSplit = false;
        $sisaCount = 0;

        if (!empty($qtyByProduk)) {
            $produkMap = Produk::query()
                ->whereIn('id', array_keys($qtyByProduk))
                ->withSum('barangMasuks as qty_masuk', 'qty')
                ->withSum('barangKeluars as qty_keluar', 'qty')
                ->get()
                ->keyBy('id');

            $stokDialokasikan = [];

            foreach ($items as $it) {
                $pid = $it['produk_id'];
                $qtyReq = (int) $it['qty'];
                $p = $produkMap->get($pid);
                $stokTotal = (int) (($p->qty_masuk ?? 0) - ($p->qty_keluar ?? 0));
                
                $stokTersedia = $stokTotal - ($stokDialokasikan[$pid] ?? 0);
                $stokTersedia = max(0, $stokTersedia);

                if ($stokTersedia <= 0) {
                    // Stok habis: semua masuk ke Sisa PO
                    $sisaItems[] = [
                        'no_po' => $data['no_po'],
                        'no_invoice' => $data['invoice_number'] ?? null,
                        'produk_id' => $pid,
                        'qty_diminta' => $qtyReq,
                        'qty_tersedia' => 0,
                        'qty_sisa' => $qtyReq,
                        'qty_jenis' => $it['qty_jenis'],
                        'harga' => (float) $it['harga'],
                        'total_sisa' => $qtyReq * (float) $it['harga'],
                        'customer' => $customerName,
                        'tanggal_po' => $data['tanggal_po'],
                        'status' => 'pending',
                        'keterangan' => 'Stok habis - seluruh pesanan masuk ke Sisa Data PO'
                    ];
                    
                    $splitMessages[] = 'Produk "' . ($p->nama_produk ?? ('ID '.$pid)) . '": Stok habis (0), seluruh pesanan ' . $qtyReq . ' pcs masuk ke Sisa Data PO.';
                    $sisaCount += $qtyReq;
                    
                } else if ($qtyReq > $stokTersedia) {
                    // Stok tidak cukup: sebagian ke PO, sisanya ke Sisa PO
                    $adjustedItem = $it;
                    $adjustedItem['qty'] = $stokTersedia;
                    $adjustedItem['total'] = $stokTersedia * (float) $it['harga'];
                    $adjustedItems->push($adjustedItem);
                    
                    $stokDialokasikan[$pid] = ($stokDialokasikan[$pid] ?? 0) + $stokTersedia;

                    $qtySisa = $qtyReq - $stokTersedia;
                    $sisaItems[] = [
                        'no_po' => $data['no_po'],
                        'no_invoice' => $data['invoice_number'] ?? null,
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
                    $hadSplit = true;
                    $sisaCount += $qtySisa;
                } else {
                    // Stok cukup
                    $adjustedItems->push($it);
                    $stokDialokasikan[$pid] = ($stokDialokasikan[$pid] ?? 0) + $qtyReq;
                }
            }
        }

        return [
            'items' => $adjustedItems->all(),
            'sisa_items' => $sisaItems,
            'split_messages' => $splitMessages,
            'had_split' => $hadSplit,
            'sisa_count' => $sisaCount,
        ];
    }

    /**
     * Simpan sisa items ke tabel sisa_po_items
     */
    public function saveSisaItems(array $sisaItems): void
    {
        if (!empty($sisaItems)) {
            foreach ($sisaItems as $sisaItem) {
                SisaPOItem::create($sisaItem);
            }
        }
    }

    /**
     * Create atau update PO items
     */
    public function syncPOItems(PO $po, array $items): void
    {
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
    }

    /**
     * Catat barang keluar otomatis
     */
    public function recordBarangKeluar(array $items, string $noPo, string $tanggalPo): void
    {
        foreach ($items as $it) {
            if ((int) $it['qty'] > 0) {
                BarangKeluar::create([
                    'produk_id' => $it['produk_id'],
                    'qty'       => (int) $it['qty'],
                    'tanggal'   => $tanggalPo,
                    'keterangan'=> 'Auto Keluar dari PO ' . trim($noPo),
                    'user_id'   => auth()->id(),
                ]);
            }
        }
    }

    /**
     * Pangkas tabel barang_keluars agar hanya menyimpan <limit> data terbaru
     */
    public function pruneBarangKeluar(int $limit = 50): void
    {
        try {
            $total = BarangKeluar::count();
            if ($total > $limit) {
                $toDelete = $total - $limit;
                $ids = BarangKeluar::orderBy('id', 'asc')
                    ->limit($toDelete)
                    ->pluck('id');
                if ($ids->isNotEmpty()) {
                    BarangKeluar::whereIn('id', $ids)->delete();
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Prune BarangKeluar gagal: ' . $e->getMessage());
        }
    }

    /**
     * Resolve customer name dari request data
     */
    public function resolveCustomerName(array $data, $request): string
    {
        $customer = null;
        if (!empty($data['customer_id'])) {
            $customer = Customer::find($data['customer_id']);
        }
        
        $customerName = $customer?->name ?? (string) $request->input('customer', '');

        // Perkuat resolusi customer: jika kosong, coba ambil dari PO sebelumnya
        if (!is_string($customerName) || trim($customerName) === '') {
            $invKey = (string) ($data['invoice_number'] ?? '');
            if ($invKey !== '') {
                $prev = PO::where('no_invoice', $invKey)
                    ->whereNotNull('customer')
                    ->whereRaw("TRIM(customer) != ''")
                    ->orderByDesc('id')
                    ->first();
                if ($prev) {
                    $customerName = (string) $prev->customer;
                    if (empty($data['customer_id'])) {
                        $matched = Customer::whereRaw('LOWER(TRIM(name)) = ?', [strtolower(trim($customerName))])->first();
                        if ($matched) {
                            $customer = $matched;
                        }
                    }
                }
            }
        }

        return $customerName;
    }

    /**
     * Bentuk nomor surat jalan
     */
    public function buildNoSuratJalan(array $data): string
    {
        return "{$data['no_surat_jalan_nomor']}/{$data['no_surat_jalan_pt']}/{$data['no_surat_jalan_tahun']}";
    }

    /**
     * Bentuk nomor invoice
     */
    public function buildNoInvoice(array $data): ?string
    {
        if (!empty($data['no_invoice_nomor']) || !empty($data['no_invoice_pt']) || !empty($data['no_invoice_tanggal']) || !empty($data['no_invoice_tahun'])) {
            $nom = $data['no_invoice_nomor'] ?? '';
            $pt  = $data['no_invoice_pt'] ?? '';
            $bln = $data['no_invoice_tanggal'] ?? '';
            $thn = $data['no_invoice_tahun'] ?? '';
            $parts = array_filter([trim($nom), trim($pt), trim((string)$bln), trim((string)$thn)], fn($v) => $v !== '');
            return implode('/', $parts);
        }
        return null;
    }

    /**
     * Resolve alamat dari customer
     */
    public function resolveAddresses(array $data, ?Customer $customer): array
    {
        $address1 = $data['address_1'] ?? '';
        $address2 = $data['address_2'] ?? '';

        if (empty($address1) || $address1 === '-') {
            $address1 = $customer->address_1 ?? '';
        }
        if (empty($address2) || $address2 === '-') {
            $address2 = $customer->address_2 ?? '';
        }

        return [$address1, $address2];
    }

    /**
     * Ekspor PO ke Excel
     */
    public function exportToExcel(PO $po, string $noSuratJalan): ?string
    {
        $templatePath = storage_path('app/template/SJ CAM & OUTSTANDING 2024.xlsm');
        if (!file_exists($templatePath)) {
            return null;
        }

        try {
            $spreadsheet = IOFactory::load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();
            $row = 10;
            
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

            $filename = 'PO_' . now()->format('Ymd_His') . '.xlsm';
            $exportPath = storage_path('app/public/exports/');

            if (!file_exists($exportPath)) {
                mkdir($exportPath, 0777, true);
            }

            $savePath = $exportPath . $filename;
            IOFactory::createWriter($spreadsheet, 'Xls')->save($savePath);

            return $savePath;
        } catch (\Throwable $e) {
            Log::error('Export Excel gagal: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Sync ke Jatuh Tempo (hanya untuk status Accept)
     */
    public function syncToJatuhTempo(PO $po, ?Customer $customer, string $invoiceKey): void
    {
        if (($po->status_approval ?? 'Pending') !== 'Accept') {
            return;
        }

        try {
            $tanggalInvoice = \Carbon\Carbon::parse($po->tanggal_po);
            $termsDays = (int) ($customer->payment_terms_days ?? 0);
            
            if ($termsDays > 0) {
                $tanggalJatuhTempo = (clone $tanggalInvoice)->addDays($termsDays);
            } else {
                $tanggalJatuhTempo = (clone $tanggalInvoice)->addMonth();
            }

            $allAcceptedPos = PO::where('no_invoice', $invoiceKey)
                ->where('status_approval', 'Accept')
                ->get();

            if ($allAcceptedPos->isNotEmpty()) {
                $allNoPo = $allAcceptedPos->pluck('no_po')->filter()->implode(', ');
                $totalTagihan = $allAcceptedPos->sum('total');
                
                $existingJT = JatuhTempo::where('no_invoice', $invoiceKey)->first();
                $jumlahTerbayar = $existingJT ? (int)($existingJT->jumlah_terbayar ?? 0) : 0;

                $jtPayload = [
                    'no_invoice' => $invoiceKey,
                    'no_po' => $allNoPo,
                    'customer' => $po->customer,
                    'tanggal_invoice' => $tanggalInvoice->format('Y-m-d'),
                    'tanggal_jatuh_tempo' => $tanggalJatuhTempo->format('Y-m-d'),
                    'jumlah_tagihan' => (int) $totalTagihan,
                    'jumlah_terbayar' => $jumlahTerbayar,
                    'sisa_tagihan' => max(0, (int)$totalTagihan - $jumlahTerbayar),
                    'status_pembayaran' => $jumlahTerbayar >= $totalTagihan ? 'Lunas' : ($jumlahTerbayar > 0 ? 'Sebagian' : 'Belum Bayar'),
                    'status_approval' => 'Pending',
                ];

                if ($existingJT) {
                    $existingJT->update($jtPayload);
                } else {
                    JatuhTempo::create($jtPayload);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('[JT] Sync JatuhTempo gagal', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Cek apakah invoice sudah Lunas (untuk blocking edit/delete)
     */
    public function isInvoiceLunas(string $noInvoice): bool
    {
        $jatuhTempo = JatuhTempo::where('no_invoice', $noInvoice)->first();
        return $jatuhTempo && $jatuhTempo->status_pembayaran === 'Lunas';
    }

    /**
     * Validasi stok untuk update (tidak boleh stok 0)
     */
    public function validateStockForUpdate(array $items): void
    {
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
    }

    /**
     * Rollback stok dengan menghapus BarangKeluar
     */
    public function rollbackStock(PO $po): void
    {
        $isAccept = (string)($po->status_approval ?? 'Pending') === 'Accept';
        if ($isAccept || empty($po->no_po)) {
            return;
        }

        try {
            DB::transaction(function() use ($po) {
                $noPoTrim = trim((string)$po->no_po);
                $items = $po->items()->get(['produk_id']);
                
                foreach ($items as $it) {
                    BarangKeluar::where('produk_id', $it->produk_id)
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
        } catch (\Throwable $e) {
            Log::warning('Rollback stock gagal: ' . $e->getMessage());
        }
    }
}
