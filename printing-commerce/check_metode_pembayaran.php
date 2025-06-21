<?php
// check_metode_pembayaran.php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$metodePembayaran = DB::select('SELECT * FROM metode_pembayaran');

echo "Data from metode_pembayaran table after seeding:\n";
echo "=========================================\n\n";

foreach ($metodePembayaran as $index => $mp) {
    echo "Record #" . ($index + 1) . ":\n";
    foreach ((array)$mp as $key => $value) {
        echo "  $key: $value\n";
    }
    echo "\n";
} 