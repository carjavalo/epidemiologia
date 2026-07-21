<?php
require __DIR__ . '/../vendor/autoload.php';

$file = 'C:/Users/Soporte/Desktop/Proyectos HUV/proahuv/proahuv/docu/SEGUIMIENTO MICROBIOLÓGICO (1).xlsx';

if (!file_exists($file)) {
    die("Archivo no encontrado: $file\n");
}

try {
    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($file);
    $reader->setReadDataOnly(true);
    $spreadsheet = $reader->load($file);
    $sheet = $spreadsheet->getActiveSheet();
    
    echo "=== Active Sheet: " . $sheet->getTitle() . " ===\n";
    
    // Read headers
    $headers = [];
    foreach ($sheet->getRowIterator(1, 1) as $row) {
        foreach ($row->getCellIterator() as $cell) {
            $col = $cell->getColumn();
            $val = $cell->getValue();
            if ($val !== null && $val !== '') {
                $headers[$col] = $val;
            }
        }
    }
    
    echo "Headers:\n";
    foreach ($headers as $col => $name) {
        echo "$col: $name\n";
    }
    
    // Find column for specialty
    $specialtyCol = null;
    foreach ($headers as $col => $name) {
        if (stripos($name, 'especialidad') !== false || stripos($name, 'cirugia') !== false || stripos($name, 'cirugía') !== false) {
            echo "Potential Specialty Column: $col ($name)\n";
            $specialtyCol = $col;
        }
    }
    
    // Also look for column T, U, V headers to verify what they are called in this excel sheet
    echo "\nColumn T: " . ($headers['T'] ?? 'N/A') . "\n";
    echo "Column U: " . ($headers['U'] ?? 'N/A') . "\n";
    echo "Column V: " . ($headers['V'] ?? 'N/A') . "\n";
    
    // Extract unique specialties if column found
    if ($specialtyCol) {
        $specialties = [];
        $highestRow = $sheet->getHighestRow();
        for ($i = 2; $i <= $highestRow; $i++) {
            $val = $sheet->getCell("$specialtyCol$i")->getValue();
            if ($val !== null && $val !== '') {
                $specialties[$val] = true;
            }
        }
        echo "\nUnique specialties count: " . count($specialties) . "\n";
        echo "Values:\n";
        foreach (array_keys($specialties) as $spec) {
            echo "- $spec\n";
        }
    } else {
        // Let's print unique values for all columns that might contain text that looks like a medical specialty
        echo "\nCould not find specific specialty column automatically. Searching all headers...\n";
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
