<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use App\Models\PO;
use Carbon\Carbon;
use App\Models\JatuhTempo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class POExportController extends Controller
{
    public function exportToExcel(Request $request)
    {
        try {
            // Tipe export: 'surat_jalan' (default) atau 'tanda_terima'
            $exportType = $request->input('export_type', 'surat_jalan');

            // Selalu coba cari PO jika ada input; dukung multi-select melalui selected_ids
            $po = null; // PO pertama untuk header
            $posList = collect(); // Kumpulan PO untuk flattened items
            // 1) selected_ids (JSON array dari view Surat Jalan)
            $selectedRaw = $request->input('selected_ids');
            if (!empty($selectedRaw)) {
                $selected = is_array($selectedRaw) ? $selectedRaw : json_decode($selectedRaw, true);
                if (is_array($selected) && !empty($selected)) {
                    $posList = \App\Models\PO::with(['produkRel', 'pengirimRel', 'items.produk'])
                        ->whereIn('id', $selected)
                        ->orderBy('tanggal_po', 'asc')
                        ->get();
                    $po = $posList->first();
                }
            }
            // 2) no_surat_jalan (string) fallback single
            if (!$po) {
                $noSuratJalan = $request->input('no_surat_jalan');
                if ($noSuratJalan) {
                    $po = \App\Models\PO::with(['produkRel', 'pengirimRel', 'items.produk'])->where('no_surat_jalan', $noSuratJalan)->first();
                    if ($po) { $posList = collect([$po]); }
                }
            }

            if (!$po && $exportType !== 'tanda_terima') {
                return response()->json([
                    'error' => 'Data PO tidak ditemukan.',
                    'hint' => 'Pilih minimal satu baris di tabel sehingga selected_ids terisi, atau kirim no_surat_jalan secara langsung.',
                ], 404);
            }

            // Path template Excel (prioritaskan file khusus jika tipe tanda_terima)
            $templateDir = storage_path('app/template');
            if ($exportType === 'tanda_terima') {
                $candidates = [
                    // Prioritas sesuai permintaan user
                    $templateDir . DIRECTORY_SEPARATOR . 'PAYMENT 2024.xls',
                    $templateDir . DIRECTORY_SEPARATOR . 'PAYMENT 2024.xlsx',
                    $templateDir . DIRECTORY_SEPARATOR . 'Tanda_Terima_Template.xlsm',
                    $templateDir . DIRECTORY_SEPARATOR . 'Tanda_Terima_Template.xlsx',
                ];
            } else {
                $candidates = [
                    $templateDir . DIRECTORY_SEPARATOR . 'Surat_Jalan_Template.xlsm',
                    $templateDir . DIRECTORY_SEPARATOR . 'Surat_Jalan_Template.xlsx',
                ];
            }
            $templatePath = null;
            foreach ($candidates as $candidate) {
                if (file_exists($candidate)) {
                    $templatePath = $candidate;
                    break;
                }
            }
            if (!$templatePath) {
                return response()->json([
                    'error' => 'Template Excel tidak ditemukan.',
                    'export_type' => $exportType,
                    'checked_paths' => $candidates,
                ], 404);
            }

            // Optimasi: tambahkan batas waktu dan memory untuk proses export
            @set_time_limit(90);
            @ini_set('memory_limit', '512M');

            $t0 = microtime(true);
            // Load template Excel - optimized: jika tanda_terima, muat hanya sheet "TANDA TERIMA"
            if ($exportType === 'tanda_terima') {
                $reader = IOFactory::createReaderForFile($templatePath);
                // PENTING: JANGAN readDataOnly agar semua styling/format template tetap utuh
                if (method_exists($reader, 'setReadDataOnly')) {
                    $reader->setReadDataOnly(false);
                }
                // Muat hanya sheet tertentu bila didukung (tetap mempertahankan styling sheet tsb)
                if (method_exists($reader, 'setLoadSheetsOnly')) {
                    $reader->setLoadSheetsOnly(['TANDA TERIMA']);
                }
                $spreadsheet = $reader->load($templatePath);
            } else {
                $spreadsheet = IOFactory::load($templatePath);
            }
            $sheet = $spreadsheet->getActiveSheet();
            \Log::info('[export-timing] load_template_ms=' . number_format((microtime(true) - $t0) * 1000, 2));

                                                                                                                                                                                                // Jika export Tanda Terima: ambil data dari semua PO terpilih seperti Invoice
            if ($exportType === 'tanda_terima') {
                if ($po && $posList->isNotEmpty()) {
                    try {
                        $active = $spreadsheet->getActiveSheet();
                        
                        // Customer dari PO pertama
                        $active->setCellValue('J14', $po->customer ?? '');
                        
                        // Isi J15 dengan nomor depan dari no_invoice (sebelum '/')
                        $nomorInv = '';
                        $sisaInv = ''; // Untuk K15: isi setelah '/' pertama
                        if (!empty($po->no_invoice)) {
                            $parts = explode('/', $po->no_invoice);
                            $nomorInv = trim((string)($parts[0] ?? '')); // Tanpa spasi
                            $sisaParts = array_slice($parts, 1);
                            $sisaInv = '/ ' . implode(' / ', $sisaParts); // Format: '/ dg / 8 / 2025'
                        }
                        $active->setCellValue('J15', $nomorInv);
                        $active->setCellValue('K15', $sisaInv);
                        
                        // Hitung sub total dari semua PO terpilih (seperti Invoice)
                        $subTotal = 0;
                        foreach ($posList as $poItem) {
                            if (!$poItem->relationLoaded('items')) { $poItem->load('items'); }
                            $subTotal += $poItem->items->sum('total');
                        }
                        $active->setCellValue('J17', $subTotal);

                        // F21: isi dengan tanggal dari tabel Surat Jalan (tanggal_po) dengan format d/mmm/yyyy
                        try {
                            if (!empty($po->tanggal_po)) {
                                $dt = Carbon::parse($po->tanggal_po);
                                $active->setCellValue('F21', ExcelDate::PHPToExcel($dt));
                                $active->getStyle('F21')->getNumberFormat()->setFormatCode('d/mmm/yyyy');
                            }
                        } catch (\Throwable $e) {
                            \Log::warning('Set F21 tanggal_po gagal: ' . $e->getMessage());
                        }

                        // E24: isi dengan deadline (tanggal_jatuh_tempo) dari form Jatuh Tempo
                        try {
                            $invoiceKey = $po->no_invoice ?: $po->no_surat_jalan; // konsisten dengan sinkronisasi
                            $jt = $invoiceKey ? JatuhTempo::where('no_invoice', $invoiceKey)->first() : null;
                            if ($jt && !empty($jt->tanggal_jatuh_tempo)) {
                                $deadline = Carbon::parse($jt->tanggal_jatuh_tempo)->format('d F Y');
                                $active->setCellValue('E24', $deadline);
                            } else {
                                // Fallback opsional: estimasi dari tanggal PO + terms 30 hari jika JatuhTempo belum ada
                                if ($po->tanggal_po) {
                                    $fallback = Carbon::parse($po->tanggal_po)->addDays(30)->format('d F Y');
                                    $active->setCellValue('E24', $fallback);
                                }
                            }
                        } catch (\Throwable $e) {
                            \Log::warning('Set E24 deadline gagal: ' . $e->getMessage());
                        }
                    } catch (\Throwable $e) {
                        \Log::warning('Set J14/J15/K15 gagal: ' . $e->getMessage());
                    }
                }

                // Nama file download
                $fileName = 'Tanda-Terima-' . now()->format('Ymd-His') . '.xlsx';

                // Set headers yang benar
                $headers = [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                    'Cache-Control' => 'max-age=0',
                ];

                // Prune folder exports: simpan 20 terbaru, hapus sisanya
                $this->pruneExports(20);

                \Log::info('[export-timing] ready_to_stream_template_only_ms=' . number_format((microtime(true) - $t0) * 1000, 2));
                return response()->streamDownload(function () use ($spreadsheet) {
                    if (ob_get_length()) { @ob_end_clean(); }
                    $writer = new Xlsx($spreadsheet);
                    if (method_exists($writer, 'setPreCalculateFormulas')) {
                        $writer->setPreCalculateFormulas(false);
                    }
                    $writer->save('php://output');
                }, $fileName, $headers);
            }

            $tFill0 = microtime(true);
            // Isi data ke sel yang sesuai
            // Bagi no_surat_jalan menjadi dua bagian untuk D10 dan F10
            $noSj = trim((string) ($po->no_surat_jalan ?? ''));
            $part1 = $noSj;
            $part2 = '';
            if ($noSj !== '') {
                // Coba split berdasarkan delimiter umum terlebih dahulu
                $delims = [' / ', '/', ' - ', '-', ' | ', '|'];
                $splitDone = false;
                foreach ($delims as $d) {
                    if (strpos($noSj, $d) !== false) {
                        [$part1, $part2] = explode($d, $noSj, 2);
                        $splitDone = true;
                        break;
                    }
                }
                if (!$splitDone) {
                    // Fallback: belah di tengah kata terdekat
                    $len = mb_strlen($noSj);
                    $mid = (int) floor($len / 2);
                    // Cari spasi terdekat ke kiri/kanan dari mid
                    $left = mb_strrpos(mb_substr($noSj, 0, $mid), ' ');
                    $rightPos = mb_strpos($noSj, ' ', $mid);
                    if ($left !== false) {
                        $part1 = trim(mb_substr($noSj, 0, $left));
                        $part2 = trim(mb_substr($noSj, $left + 1));
                    } elseif ($rightPos !== false) {
                        $part1 = trim(mb_substr($noSj, 0, $rightPos));
                        $part2 = trim(mb_substr($noSj, $rightPos + 1));
                    } else {
                        // Tidak ada spasi, pakai pembagian kasar
                        $part1 = trim(mb_substr($noSj, 0, $mid));
                        $part2 = trim(mb_substr($noSj, $mid));
                    }
                }
            }
            // Tampilkan bagian pertama di D10 dengan akhiran ' /' sebagai pemisah
            $sheet->setCellValue('D10', $part1 !== '' ? ($part1 . ' /') : '');
            $sheet->setCellValue('F10', $part2);
            // Multi-PO detection: kosongkan E12 (No PO) jika lebih dari satu PO dipilih
            $uniquePO = [];
            foreach ($posList as $poCheck) { if (!empty($poCheck->no_po)) { $uniquePO[$poCheck->no_po] = true; } }
            $isMultiPO = count($uniquePO) > 1;
            $sheet->setCellValue('E12', $isMultiPO ? '' : ($po->no_po ?? ''));
            $sheet->setCellValue('J6', $po->customer);
            // tanggal_po sekarang diletakkan pada range merge K1:N2, cukup tulis ke K1
            $sheet->setCellValue('K1', $po->tanggal_po ? Carbon::parse($po->tanggal_po)->format('d F Y') : '');
            // Baris item: dari D14 kebawah (hingga D24), beserta qty (A) dan jenis (C)
            $startRow = 14;
            $endRow = 24;
            // Kosongkan area terlebih dahulu agar tidak ada sisa data
            for ($r = $startRow; $r <= $endRow; $r++) {
                $sheet->setCellValue("A{$r}", '');
                $sheet->setCellValue("C{$r}", '');
                $sheet->setCellValue("D{$r}", '');
            }
            // Flatten semua item dari semua PO terpilih
            $row = $startRow;
            foreach ($posList as $poIt) {
                if (!$poIt->relationLoaded('items')) { $poIt->load('items.produk'); }
                foreach ($poIt->items as $item) {
                    if ($row > $endRow) { break 2; }
                    $sheet->setCellValue("A{$row}", (int)($item->qty ?? 0));
                    $sheet->setCellValue("C{$row}", strtoupper($item->qty_jenis ?? 'PCS'));
                    $produkBase = $item->produk?->nama_produk ?? '';
                    $produkOut = $isMultiPO && !empty($poIt->no_po)
                        ? (trim($produkBase) . ' (' . trim($poIt->no_po) . ')')
                        : $produkBase;
                    $sheet->setCellValue("D{$row}", $produkOut);
                    // Jarakkan 1 baris kosong antar item
                    $row += 2;
                }
            }
            $sheet->setCellValue('G2', $po->harga);
            $sheet->setCellValue('H2', $po->total);

            // Kendaraan dan No Polisi - ambil langsung dari kolom tabel pos
            $kendaraanName = $po->kendaraan ?? '';
            $sheet->setCellValue('L10', $kendaraanName);
            $noPolisi = $po->no_polisi ?? '';
            $sheet->setCellValue('K12', $noPolisi);

            // Tambahan alamat
            $sheet->setCellValue('J7', $po->alamat_1);
            $sheet->setCellValue('J8', $po->alamat_2);
            // Pengirim di H26 (fallback ke relasi jika kolom kosong)
            $sheet->setCellValue('H26', $po->pengirim ?? ($po->pengirimRel->nama ?? ''));
            \Log::info('[export-timing] fill_cells_ms=' . number_format((microtime(true) - $tFill0) * 1000, 2));

            // Nama file download
            $fileName = 'PO-' . now()->format('Ymd-His') . '.xlsx';

            // Set headers yang benar
            $headers = [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                'Cache-Control' => 'max-age=0',
            ];

            // Prune folder exports: simpan 20 terbaru, hapus sisanya
            $this->pruneExports(20);

            // Download langsung ke browser
            \Log::info('[export-timing] ready_to_stream_ms=' . number_format((microtime(true) - $t0) * 1000, 2));
            return response()->streamDownload(function () use ($spreadsheet) {
                // Bersihkan output buffer agar header tidak tertabrak
                if (ob_get_length()) { @ob_end_clean(); }
                $writer = new Xlsx($spreadsheet);
                // Matikan pre-calculate formula untuk mempercepat
                if (method_exists($writer, 'setPreCalculateFormulas')) {
                    $writer->setPreCalculateFormulas(false);
                }
                $writer->save('php://output');
            }, $fileName, $headers);

        } catch (\Exception $e) {
            \Log::error('Excel Export Error: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan saat export Excel.'], 500);
        }
    }

    /**
     * Jaga direktori exports agar tidak menumpuk.
     * Menyimpan hanya 20 file Excel terbaru di `storage/app/public/exports`.
     */
    private function pruneExports(int $keep = 20): void
    {
        try {
            $disk = Storage::disk('public');
            // Ambil semua file di folder exports
            $files = collect($disk->files('exports'))
                // Filter hanya file Excel yang relevan
                ->filter(function ($path) {
                    $name = basename($path);
                    $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                    if (!in_array($ext, ['xls', 'xlsx', 'xlsm'])) return false;
                    // Nama yang umum pada app ini
                    return Str::startsWith($name, ['PO_', 'PO-', 'Tanda-Terima-']);
                })
                // Map dengan timestamp untuk sorting
                ->map(function ($path) use ($disk) {
                    $ts = 0;
                    try { $ts = $disk->lastModified($path); } catch (\Throwable $e) {}
                    return ['path' => $path, 'ts' => $ts];
                })
                ->sortByDesc('ts')
                ->values();

            if ($files->count() <= $keep) return;

            // Sisakan 20 terbaru, hapus sisanya
            $toDelete = $files->slice($keep)->pluck('path');
            foreach ($toDelete as $p) {
                try { $disk->delete($p); } catch (\Throwable $e) { /* ignore */ }
            }
        } catch (\Throwable $e) {
            \Log::warning('Prune exports gagal: ' . $e->getMessage());
        }
    }
}

