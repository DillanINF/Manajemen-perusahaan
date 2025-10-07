<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class ExcelTemplateController extends Controller
{
    public function generateFromExcel()
    {
        try {
            // Cari file template Excel secara fleksibel
            $candidates = [
                'gaji_borongan.xlsx', // nama baru (benar)
                'gaji_borongan.xlxs', // antisipasi salah ketik ekstensi
                'GAJI.xlsx',          // fallback lama
            ];

            $excelFile = null;
            $searchedPaths = [];

            foreach ($candidates as $name) {
                // Prioritas: storage/app/template/
                $path1 = storage_path('app/template/' . $name);
                $searchedPaths[] = $path1;
                if (file_exists($path1)) { $excelFile = $path1; break; }

                // Alternatif: public/template/ (bila user meletakkan di public)
                $path2 = public_path('template/' . $name);
                $searchedPaths[] = $path2;
                if (file_exists($path2)) { $excelFile = $path2; break; }
            }

            if (!$excelFile) {
                return response()->json([
                    'error' => 'File Excel tidak ditemukan. Letakkan di storage/app/template atau public/template.',
                    'looked_in' => $searchedPaths,
                    'expected' => $candidates,
                ], 404);
            }
            
            // Load Excel
            $spreadsheet = IOFactory::load($excelFile);
            $worksheet = $spreadsheet->getActiveSheet();
            $sheetTitle = $worksheet->getTitle();

            // Terapkan edits yang tersimpan (overlay) berdasarkan user + periode jika ada
            $periodYear = $request->query('period_year');
            $periodMonth = $request->query('period_month');
            $userId = Auth::id();
            if ($userId && Schema::hasTable('excel_sheet_edits')) {
                $edits = DB::table('excel_sheet_edits')
                    ->where('user_id', $userId)
                    ->where('sheet_name', $sheetTitle)
                    ->whereNull('deleted_at')
                    ->when($periodYear, fn($q) => $q->where('period_year', (int)$periodYear))
                    ->when($periodMonth, fn($q) => $q->where('period_month', (int)$periodMonth))
                    ->orderByDesc('updated_at')
                    ->first();

                if ($edits && !empty($edits->cells)) {
                    $cells = json_decode($edits->cells, true) ?? [];
                    foreach ($cells as $cell) {
                        $ref = $cell['ref'] ?? null;
                        if (!$ref) continue;
                        $val = $cell['value'] ?? '';
                        try { 
                            $worksheet->setCellValue($ref, $val); 
                        } catch (\Throwable $t) { 
                            // ignore invalid cell refs
                        }
                    }
                }
            }
            
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();
            $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
            
            // Baca SELURUH Excel file untuk mendapatkan semua data dan styling
            $excelData = [];
            $mergedCells = [];
            $columnWidths = [];
            $rowHeights = [];
            
            // Get merged cells info
            foreach ($worksheet->getMergeCells() as $mergeRange) {
                $mergedCells[] = $mergeRange;
            }
            
            // Get column widths
            foreach ($worksheet->getColumnDimensions() as $colDimension) {
                $columnWidths[$colDimension->getColumnIndex()] = $colDimension->getWidth();
            }
            
            // Get row heights  
            foreach ($worksheet->getRowDimensions() as $rowDimension) {
                $rowHeights[$rowDimension->getRowIndex()] = $rowDimension->getRowHeight();
            }
            
            // Baca semua data dengan styling lengkap
            for ($row = 1; $row <= $highestRow; $row++) {
                $rowData = [];
                for ($col = 1; $col <= $highestColumnIndex; $col++) {
                    $cell = $worksheet->getCellByColumnAndRow($col, $row);
                    $value = $cell->getCalculatedValue();
                    $style = $cell->getStyle();
                    
                    $columnLetter = Coordinate::stringFromColumnIndex($col);
                    $rowData[$columnLetter] = [
                        'value' => $value,
                        'bold' => $style->getFont()->getBold(),
                        'bgColor' => $this->getBgColor($style),
                        'border' => $this->getBorderStyle($style),
                        'fontSize' => $style->getFont()->getSize(),
                        'alignment' => $style->getAlignment()->getHorizontal(),
                        'verticalAlignment' => $style->getAlignment()->getVertical(),
                        'fontColor' => $this->getFontColor($style),
                        'cellWidth' => $columnWidths[$col] ?? 'auto',
                        'cellHeight' => $rowHeights[$row] ?? 'auto'
                    ];
                }
                $excelData[$row] = $rowData;
            }
            
            // Generate HTML PERSIS seperti Excel template
            $html = $this->generateExactHTML($excelData, $mergedCells, $highestRow, $highestColumnIndex);
            
            return response()->json([
                'success' => true,
                'html' => $html,
                'info' => [
                    'rows' => $highestRow,
                    'columns' => $highestColumnIndex,
                    'worksheet' => $worksheet->getTitle()
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    private function getBgColor($style)
    {
        if ($style->getFill()->getFillType() !== \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_NONE) {
            return $style->getFill()->getStartColor()->getRGB();
        }
        return 'FFFFFF';
    }
    
    private function getFontColor($style)
    {
        return $style->getFont()->getColor()->getRGB() ?? '000000';
    }
    
    private function getBorderStyle($style)
    {
        $borders = $style->getBorders();
        return [
            'top' => $borders->getTop()->getBorderStyle(),
            'right' => $borders->getRight()->getBorderStyle(),
            'bottom' => $borders->getBottom()->getBorderStyle(),
            'left' => $borders->getLeft()->getBorderStyle()
        ];
    }
    
    private function generateExactHTML($excelData, $mergedCells, $maxRows, $maxCols)
    {
        $html = '<div class="excel-template-exact" style="font-family: Arial, sans-serif; overflow-x: auto; overflow-y: hidden; padding:0; margin:0; width:100%;">';
        
        // CSS khusus untuk memastikan input bisa diklik
        $html .= '<style>
            .excel-input-cell {
                pointer-events: auto !important;
                user-select: text !important;
                -webkit-user-select: text !important;
                -moz-user-select: text !important;
                -ms-user-select: text !important;
            }
            .excel-input-cell:hover {
                background-color: #f0f8ff !important;
                cursor: text !important;
            }
            .excel-template-exact td {
                position: relative;
                overflow: visible !important;
            }
        </style>';
        
        // Tabel dilebarkan untuk menggunakan ruang maksimal
        $html .= '<table style="border-collapse: collapse; width: 100%; min-width: 1200px; table-layout: auto; margin:0;">';
        
        // Parameter area data (berdasarkan analisis template)
        $dataStartRow = 54; // hanya area data yang editable
        // Tidak ada header yang editable
        $alwaysEditable = [];
        
        // counter debug dihapus (tidak diperlukan)

        // Proses merged cells untuk lookup yang efisien
        $mergedCellMap = [];
        foreach ($mergedCells as $range) {
            [$startCell, $endCell] = explode(':', $range);
            [$startCol, $startRow] = Coordinate::coordinateFromString($startCell);
            [$endCol, $endRow] = Coordinate::coordinateFromString($endCell);
            $startColIndex = Coordinate::columnIndexFromString($startCol);
            $endColIndex = Coordinate::columnIndexFromString($endCol);

            for ($r = $startRow; $r <= $endRow; $r++) {
                for ($c = $startColIndex; $c <= $endColIndex; $c++) {
                    if ($r == $startRow && $c == $startColIndex) {
                        $mergedCellMap["{$r}-{$c}"] = [
                            'type' => 'master',
                            'colspan' => ($endColIndex - $startColIndex) + 1,
                            'rowspan' => ($endRow - $startRow) + 1,
                        ];
                    } else {
                        $mergedCellMap["{$r}-{$c}"] = ['type' => 'slave'];
                    }
                }
            }
        }

        // Generate PERSIS setiap sel dari Excel template
        foreach ($excelData as $rowNum => $rowData) {
            $html .= '<tr>';
            
            for ($col = 1; $col <= $maxCols; $col++) {
                // Cek apakah sel ini bagian dari merge
                $mergeInfo = $mergedCellMap["{$rowNum}-{$col}"] ?? null;
                if ($mergeInfo && $mergeInfo['type'] === 'slave') {
                    continue; // Jangan render sel ini, karena sudah digabung
                }

                $columnLetter = Coordinate::stringFromColumnIndex($col);
                $cellData = $rowData[$columnLetter] ?? [
                    'value' => '', 'bold' => false, 'bgColor' => 'FFFFFF', 
                    'border' => [], 'fontSize' => 11, 'alignment' => 'general',
                    'verticalAlignment' => 'bottom', 'fontColor' => '000000',
                    'cellWidth' => 'auto', 'cellHeight' => 'auto'
                ];
                
                // Build cell style 100% dari Excel
                $cellStyle = '';
                
                // Background color dari Excel
                if ($cellData['bgColor'] !== 'FFFFFF') {
                    $cellStyle .= 'background-color: #' . $cellData['bgColor'] . '; ';
                }
                
                // Font styling dari Excel
                if ($cellData['bold']) {
                    $cellStyle .= 'font-weight: bold; ';
                }
                
                $cellStyle .= 'font-size: ' . ($cellData['fontSize'] ?? 11) . 'px; ';
                $cellStyle .= 'color: #' . ($cellData['fontColor'] ?? '000000') . '; ';
                
                // Border dari Excel
                if (is_array($cellData['border'])) {
                    foreach (['top', 'right', 'bottom', 'left'] as $side) {
                        if (!empty($cellData['border'][$side]) && $cellData['border'][$side] !== 'none') {
                            $cellStyle .= 'border-' . $side . ': 1px solid #000; ';
                        }
                    }
                } else {
                    $cellStyle .= 'border: 1px solid #ccc; '; // Default border
                }
                
                // Text alignment dari Excel
                $alignment = $cellData['alignment'] ?? 'general';
                if ($alignment === 'left') {
                    $cellStyle .= 'text-align: left; ';
                } elseif ($alignment === 'center') {
                    $cellStyle .= 'text-align: center; ';
                } elseif ($alignment === 'right') {
                    $cellStyle .= 'text-align: right; ';
                }
                
                // Vertical alignment dari Excel
                $vAlignment = $cellData['verticalAlignment'] ?? 'bottom';
                if ($vAlignment === 'top') {
                    $cellStyle .= 'vertical-align: top; ';
                } elseif ($vAlignment === 'center') {
                    $cellStyle .= 'vertical-align: middle; ';
                } else {
                    $cellStyle .= 'vertical-align: bottom; ';
                }
                
                // Cell dimensions dari Excel
                // Lebar kolom Excel -> px (gunakan default 8.43 jika auto)
                $colWidth = $cellData['cellWidth'];
                if ($colWidth === 'auto' || !is_numeric($colWidth)) {
                    $colWidth = 8.43; // default Excel column width (Arial 11)
                }
                $widthPx = $this->excelColWidthToPx($colWidth);
                $cellStyle .= 'width: ' . $widthPx . 'px; min-width: ' . $widthPx . 'px; max-width: ' . $widthPx . 'px; box-sizing: border-box; ';

                // Tinggi baris (jika spesifik)
                $rowHeight = $cellData['cellHeight'];
                if ($rowHeight !== 'auto' && is_numeric($rowHeight)) {
                    $heightPx = $this->excelRowHeightToPx($rowHeight);
                    if ($heightPx) {
                        $cellStyle .= 'height: ' . $heightPx . 'px; ';
                    }
                }
                
                $cellStyle .= 'padding: 2px; white-space: nowrap; ';
                
                // Terapkan colspan dan rowspan jika sel ini adalah master dari merge
                $colspan = $mergeInfo['colspan'] ?? 1;
                $rowspan = $mergeInfo['rowspan'] ?? 1;
                // Sematkan data attributes agar mudah diakses JS (selalu ada meski bukan input)
                $html .= '<td style="' . $cellStyle . '"'
                    . ' data-row="' . (int)$rowNum . '"'
                    . ' data-col="' . (int)$col . '"'
                    . ' data-cell="' . htmlspecialchars($columnLetter . (string)$rowNum) . '"'
                    . ($colspan > 1 ? " colspan='{$colspan}'" : '')
                    . ($rowspan > 1 ? " rowspan='{$rowspan}'" : '')
                    . '>';
                
                // Cell content
                $value = $cellData['value'];
                
                // Tentukan apakah sel bisa di-edit
                // Tambahan: beberapa sel header tertentu (mis. A1) juga boleh di-edit
                $cellRef = $columnLetter . (string)$rowNum;
                $isEditable = ($rowNum >= $dataStartRow);
                
                if ($isEditable) {
                    // Input field untuk area data (tetap tampil walau ada nilai)
                    $prefill = isset($value) ? htmlspecialchars((string)$value) : '';
                    $html .= '<input type="text" value="' . $prefill . '" ';
                    // Tandai alamat sel untuk penyimpanan
                    $html .= 'data-cell="' . htmlspecialchars($cellRef) . '" ';
                    $html .= 'data-row="' . (int)$rowNum . '" ';
                    $html .= 'data-col="' . (int)$col . '" ';
                    $html .= 'class="excel-input-cell" ';
                    $html .= 'style="width: 100%; height: 100%; border: none; background: transparent; ';
                    $html .= 'font-size: inherit; font-weight: inherit; color: inherit; text-align: inherit; ';
                    $html .= 'padding: 2px; margin: 0; outline: none; cursor: text; z-index: 10; position: relative;" ';
                    // Hilangkan efek warna fokus dan tooltip agar tidak bentrok dengan hover lain
                    $html .= 'onclick="this.focus();" ';
                    $html .= '>';
                } else {
                    // Static content dari Excel, bungkus dalam span agar bisa di-update via JS
                    $html .= '<span class="excel-static-cell"'
                        . ' data-row="' . (int)$rowNum . '"'
                        . ' data-col="' . (int)$col . '"'
                        . ' data-cell="' . htmlspecialchars($cellRef) . '">';
                    if (!empty($value)) {
                        $html .= htmlspecialchars($value);
                    } else {
                        $html .= '&nbsp;';
                    }
                    $html .= '</span>';
                }
                
                $html .= '</td>';
            }
            
            $html .= '</tr>';
        }
        
        $html .= '</table>';
        
        // Hapus debug block agar scrollbar menempel ke tabel
        
        $html .= '</div>';
        
        return $html;
    }

    /**
     * Konversi lebar kolom Excel (dalam unit karakter) ke pixels.
     * Rumus pendekatan umum: pixels ≈ floor(width * 7 + 5). Nilai 7 adalah rata-rata lebar karakter dan 5 padding bawaan.
     * Referensi: praktik umum konversi Excel width ke px.
     */
    private function excelColWidthToPx($width)
    {
        if (!is_numeric($width)) {
            $width = 8.43; // default Excel
        }
        $pixels = (int) floor(($width * 7) + 5);
        return max($pixels, 1);
    }

    /**
     * Konversi tinggi baris Excel (sudah dalam px jika di-set),
     * jika otomatis biarkan null agar browser menentukan.
     */
    private function excelRowHeightToPx($height)
    {
        if (!is_numeric($height)) {
            return null;
        }
        return (int) round($height);
    }
    
    /**
     * Simpan perubahan input template Excel ke database.
     */
    public function saveEdits(Request $request)
    {
        $validated = $request->validate([
            'sheet_name'   => 'required|string|max:255',
            'period_year'  => 'nullable|integer|min:2000|max:2100',
            'period_month' => 'nullable|integer|min:1|max:12',
            'cells'        => 'required|array',
        ]);

        $userId = Auth::id();
        $now = now();

        // Simpan satu record per kombinasi user+sheet+periode (upsert sederhana)
        $existing = DB::table('excel_sheet_edits')
            ->where('user_id', $userId)
            ->where('sheet_name', $validated['sheet_name'])
            ->whereNull('deleted_at')
            ->when(isset($validated['period_year']), fn($q) => $q->where('period_year', $validated['period_year']))
            ->when(isset($validated['period_month']), fn($q) => $q->where('period_month', $validated['period_month']))
            ->first();

        $payload = [
            'user_id'      => $userId,
            'sheet_name'   => $validated['sheet_name'],
            'period_year'  => $validated['period_year'] ?? null,
            'period_month' => $validated['period_month'] ?? null,
            'cells'        => json_encode($validated['cells']),
            'updated_at'   => $now,
        ];

        if ($existing) {
            DB::table('excel_sheet_edits')->where('id', $existing->id)->update($payload);
            $id = $existing->id;
        } else {
            $payload['created_at'] = $now;
            $id = DB::table('excel_sheet_edits')->insertGetId($payload);
        }

        return response()->json([
            'success' => true,
            'id' => $id,
            'saved_cells' => count($validated['cells'] ?? []),
        ]);
    }
}
