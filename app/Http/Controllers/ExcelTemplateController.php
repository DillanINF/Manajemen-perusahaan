<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class ExcelTemplateController extends Controller
{
    public function generateFromExcel()
    {
        try {
            $excelFile = storage_path('app/template/GAJI.xlsx');
            
            if (!file_exists($excelFile)) {
                return response()->json(['error' => 'File Excel tidak ditemukan'], 404);
            }
            
            // Load Excel
            $spreadsheet = IOFactory::load($excelFile);
            $worksheet = $spreadsheet->getActiveSheet();
            
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();
            $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
            
            // Baca SEMUA data Excel tanpa filter
            $excelData = [];
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
                        'border' => $this->hasBorder($style),
                        'fontSize' => $style->getFont()->getSize(),
                        'alignment' => $style->getAlignment()->getHorizontal()
                    ];
                }
                $excelData[$row] = $rowData;
            }
            
            // Generate HTML PERSIS seperti Excel
            $html = $this->generateExactHTML($excelData, $highestRow, $highestColumnIndex);
            
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
    
    private function hasBorder($style)
    {
        return $style->getBorders()->getTop()->getBorderStyle() !== \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_NONE;
    }
    
    private function generateExactHTML($excelData, $maxRows, $maxCols)
    {
        $html = '<div class="excel-exact-replica border-2 border-black bg-white" style="font-family: Arial, sans-serif;">';
        $html .= '<table class="w-full border-collapse" style="table-layout: fixed; width: 100%;">';
        
        // Generate EXACT table dari Excel
        foreach ($excelData as $rowNum => $rowData) {
            $html .= '<tr>';
            
            for ($col = 1; $col <= $maxCols; $col++) {
                $columnLetter = Coordinate::stringFromColumnIndex($col);
                $cellData = $rowData[$columnLetter] ?? ['value' => '', 'bold' => false, 'bgColor' => 'FFFFFF', 'border' => false];
                
                // Cell styling
                $cellStyle = 'border: 1px solid #000; padding: 2px; height: 20px; vertical-align: middle;';
                
                if ($cellData['bgColor'] !== 'FFFFFF') {
                    $cellStyle .= ' background-color: #' . $cellData['bgColor'] . ';';
                }
                
                if ($cellData['bold']) {
                    $cellStyle .= ' font-weight: bold;';
                }
                
                // Determine cell width based on column
                $width = $this->getExactColumnWidth($col);
                $cellStyle .= ' width: ' . $width . 'px; min-width: ' . $width . 'px;';
                
                $html .= '<td style="' . $cellStyle . '">';
                
                // Cell content
                $value = $cellData['value'];
                if (empty($value) || (is_numeric($value) && $rowNum > 60)) {
                    // Input field untuk sel kosong atau angka di area data
                    $html .= '<input type="text" value="' . htmlspecialchars($value) . '" ';
                    $html .= 'style="width: 100%; height: 100%; border: none; background: transparent; font-size: 11px; padding: 0;" ';
                    $html .= 'onchange="calculateExcelRow(' . $rowNum . ', ' . $col . ')">';
                } else {
                    // Static text
                    $fontSize = $cellData['fontSize'] ?? 11;
                    $html .= '<span style="font-size: ' . $fontSize . 'px;">' . htmlspecialchars($value) . '</span>';
                }
                
                $html .= '</td>';
            }
            
            $html .= '</tr>';
        }
        
        $html .= '</table>';
        $html .= '</div>';
        
        // Add calculation JavaScript
        $html .= '<script>
        function calculateExcelRow(row, col) {
            console.log("Calculating row " + row + ", col " + col);
            // Add your calculation logic here
        }
        </script>';
        
        return $html;
    }
    
    private function getExactColumnWidth($colIndex)
    {
        // Width berdasarkan kolom Excel asli
        $widths = [
            1 => 80,   // A - JENIS PEKERJAAN
            2 => 60,   // B - ITEM  
            3 => 80,   // C - TANGGAL
            4 => 80,   // D - JUMLAH
            5 => 100,  // E - BIAYA PRODUKSI
            6 => 120,  // F - KETERANGAN
            7 => 80,   // G - UPAH PERKEP
        ];
        
        // Default width untuk kolom lainnya
        return $widths[$colIndex] ?? 50;
    }
}
