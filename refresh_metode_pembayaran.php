<?php

// Include autoloader and bootstrap Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

echo "Refreshing payment methods table...\n";

try {
    // Step 1: Truncate the table
    DB::table('metode_pembayaran')->truncate();
    echo "Cleared existing payment methods.\n";
    
    // Step 2: Insert the three new payment methods
    $paymentMethods = [
        [
            'uuid' => Str::uuid()->toString(),
            'nama_metode_pembayaran' => 'BNI',
            'no_metode_pembayaran' => '123456789',
            'deskripsi_1' => 'Rekening Ini digunakan untuk pembayaran',
            'deskripsi_2' => 'Pastikan transfer ke rekening yang benar',
            'thumbnail' => 'bni.jpg',
            'icon' => 'bni-icon.png',
            'bahan_poster' => 'Art Paper',
            'ukuran_poster' => 'A3',
            'total_harga_poster' => '150.000'
        ],
        [
            'uuid' => Str::uuid()->toString(),
            'nama_metode_pembayaran' => 'Mandiri',
            'no_metode_pembayaran' => '987654321',
            'deskripsi_1' => 'Rekening Ini digunakan untuk pembayaran',
            'deskripsi_2' => 'Pastikan transfer ke rekening yang benar',
            'thumbnail' => 'mandiri.jpg',
            'icon' => 'mandiri-icon.png',
            'bahan_poster' => 'Art Paper',
            'ukuran_poster' => 'A3',
            'total_harga_poster' => '150.000'
        ],
        [
            'uuid' => Str::uuid()->toString(),
            'nama_metode_pembayaran' => 'OVO',
            'no_metode_pembayaran' => '081234567890',
            'deskripsi_1' => 'Rekening Ini digunakan untuk pembayaran',
            'deskripsi_2' => 'Pastikan transfer ke rekening yang benar',
            'thumbnail' => 'ovo.jpg',
            'icon' => 'ovo-icon.png',
            'bahan_poster' => 'Art Paper',
            'ukuran_poster' => 'A3',
            'total_harga_poster' => '150.000'
        ]
    ];

    // Insert the payment methods
    foreach ($paymentMethods as $method) {
        DB::table('metode_pembayaran')->insert($method);
    }
    
    echo "Successfully added payment methods: BNI, Mandiri, and OVO.\n";
    
    // Verify the payment methods
    $methods = DB::table('metode_pembayaran')->get();
    echo "\nCurrent payment methods:\n";
    foreach ($methods as $method) {
        echo "- {$method->nama_metode_pembayaran} ({$method->no_metode_pembayaran})\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 