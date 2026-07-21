<?php
require __DIR__ . '/../vendor/autoload.php';

$excels = [
    'pacientes_proa' => 'C:/Users/Soporte/Desktop/Proyectos HUV/proahuv/proahuv/docu/PACIENTES PROA - 2025 FINAL(1).xlsx',
    'seguimiento' => 'C:/Users/Soporte/Desktop/Proyectos HUV/proahuv/proahuv/docu/Filtro _ Seguimiento Microbiologico.xlsx'
];

$output = "";

foreach ($excels as $key => $path) {
    if (!file_exists($path)) {
        $output .= "File not found: $path\n\n";
        continue;
    }
    $output .= "=== File: " . basename($path) . " ===\n";
    try {
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getActiveSheet();
        
        $output .= "Active Sheet: " . $sheet->getTitle() . "\n";
        
        // Let's get the first row (headers)
        $headers = [];
        foreach ($sheet->getRowIterator(1, 1) as $row) {
            foreach ($row->getCellIterator() as $cell) {
                $col = $cell->getColumn();
                $val = $cell->getValue();
                if ($val !== null && $val !== '') {
                    $headers[] = "$col: $val";
                }
            }
        }
        $output .= "Headers:\n" . implode("\n", $headers) . "\n";
        
        // If there's a column related to Especialidad, let's find it
        $especialidadCol = null;
        foreach ($headers as $h) {
            if (stripos($h, 'especialidad') !== false || stripos($h, 'cirugia') !== false || stripos($h, 'especial') !== false) {
                $output .= "Potential match: $h\n";
            }
        }
        
        // Let's collect unique values from column T, U, V and Especialidad if found
        // Let's check what values are in T, U, V for the first 50 rows
        $output .= "\nFirst 10 rows for T, U, V:\n";
        for ($i = 2; $i <= 15; $i++) {
            $tVal = $sheet->getCell("T$i")->getValue();
            $uVal = $sheet->getCell("U$i")->getValue();
            $vVal = $sheet->getCell("V$i")->getValue();
            $output .= "Row $i - T (Sitio): '$tVal', U (Tipo): '$uVal', V (Clasificación): '$vVal'\n";
        }
        
        // Let's look for a column that might contain specialties
        // Let's print unique values from columns that could be Specialty
        // In PACIENTES PROA, let's search for columns like "ESPECIALIDAD"
        // Let's scan headers for Especialidad
        $specCol = null;
        foreach ($headers as $h) {
            if (preg_match('/(especialidad|esp_)/i', $h)) {
                preg_match('/^([A-Z]+):/i', $h, $m);
                $specCol = $m[1] ?? null;
            }
        }
        if ($specCol) {
            $output .= "\nUnique values in specialty column ($specCol):\n";
            $specs = [];
            for ($i = 2; $i <= 1000; $i++) {
                $val = $sheet->getCell("$specCol$i")->getValue();
                if ($val !== null && $val !== '') {
                    $specs[$val] = true;
                }
            }
            $output .= implode("\n", array_keys($specs)) . "\n";
        }
        
    } catch (\Exception $e) {
        $output .= "Error: " . $e->getMessage() . "\n";
    }
    $output .= "\n======================================\n\n";
}

file_put_contents(__DIR__ . '/excel_analysis.txt', $output);
echo "Analysis completed. Results written to scratch/excel_analysis.txt\n";
