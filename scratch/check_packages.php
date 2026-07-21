<?php
$lock = json_decode(file_get_contents(__DIR__ . '/../composer.lock'), true);
$packages = array_merge($lock['packages'] ?? [], $lock['packages-dev'] ?? []);
foreach ($packages as $p) {
    if (stripos($p['name'], 'spreadsheet') !== false || stripos($p['name'], 'phpoffice') !== false) {
        echo "Found package: " . $p['name'] . " (" . $p['version'] . ")\n";
    }
}
echo "Checked " . count($packages) . " packages.\n";
