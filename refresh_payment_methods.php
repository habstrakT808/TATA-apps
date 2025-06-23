<?php
// refresh_payment_methods.php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Clear existing payment methods table
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

echo "Refreshing payment methods table...\n";

try {
    // Step 1: Delete all existing payment methods
    DB::table('metode_pembayaran')->delete();
    echo "Cleared existing payment methods.\n";
    
    // Step 2: Insert the three payment methods with proper UUIDs
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

    // Insert each payment method individually to ensure all are added
    foreach ($paymentMethods as $method) {
        DB::table('metode_pembayaran')->insert($method);
    }

    echo "Successfully added 3 payment methods: BNI, Mandiri, and OVO.\n";

    // Verify data was inserted
    $count = DB::table('metode_pembayaran')->count();
    echo "Total payment methods in database: {$count}\n";
    
    // Show all payment methods
    $methods = DB::table('metode_pembayaran')->get();
    echo "\nCurrent payment methods:\n";
    echo "------------------------\n";
    foreach ($methods as $method) {
        echo "- {$method->nama_metode_pembayaran} ({$method->no_metode_pembayaran})\n";
    }
    
    echo "\nPayment methods refresh completed successfully!\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
} 