<?php
require __DIR__ . '/vendor/autoload.php';

$file = 'C:/Users/Soporte/Desktop/Proyectos HUV/proahuv/proahuv/docu/PACIENTES PROA - 2025 FINAL(1).xlsx';

if (!file_exists($file)) {
    die("Archivo no encontrado: $file\n");
}

$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
$spreadsheet = $reader->load($file);
$sheet = $spreadsheet->getActiveSheet();

// Mostrar las primeras 5 filas para ver la estructura
echo "=== ENCABEZADOS (fila 1) ===\n";
$headers = [];
foreach ($sheet->getRowIterator(1, 1) as $row) {
    foreach ($row->getCellIterator() as $cell) {
        $val = $cell->getValue();
        if ($val !== null && $val !== '') {
            $headers[] = $cell->getColumn() . ': ' . $val;
        }
    }
}
echo implode("\n", $headers) . "\n";

echo "\n=== FILA 2 (primer dato) ===\n";
foreach ($sheet->getRowIterator(2, 2) as $row) {
    foreach ($row->getCellIterator() as $cell) {
        $val = $cell->getValue();
        if ($val !== null && $val !== '') {
            echo $cell->getColumn() . ': ' . $val . "\n";
        }
    }
}
