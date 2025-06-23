<?php
// seed_metode_pembayaran.php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Insert data into metode_pembayaran table
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

$metodePembayaran = [
    [
        'uuid' => Str::uuid(),
        'nama_metode_pembayaran' => 'Mandiri',
        'no_metode_pembayaran' => '987654321',
        'deskripsi_1' => 'TATA Design Studio',
        'deskripsi_2' => 'Transfer ke rekening di atas',
        'thumbnail' => 'mandiri.jpg',
        'icon' => 'mandiri-icon.png',
        'bahan_poster' => 'Art Paper',
        'ukuran_poster' => 'A3',
        'total_harga_poster' => '150.000',
    ],
    [
        'uuid' => Str::uuid(),
        'nama_metode_pembayaran' => 'BNI',
        'no_metode_pembayaran' => '123456789',
        'deskripsi_1' => 'TATA Design Studio',
        'deskripsi_2' => 'Transfer ke rekening di atas',
        'thumbnail' => 'bni.jpg',
        'icon' => 'bni-icon.png',
        'bahan_poster' => 'Art Paper',
        'ukuran_poster' => 'A3',
        'total_harga_poster' => '150.000',
    ],
    [
        'uuid' => Str::uuid(),
        'nama_metode_pembayaran' => 'OVO',
        'no_metode_pembayaran' => '081234567890',
        'deskripsi_1' => 'TATA Design Studio',
        'deskripsi_2' => 'Transfer ke rekening di atas',
        'thumbnail' => 'ovo.jpg',
        'icon' => 'ovo-icon.png',
        'bahan_poster' => 'Art Paper',
        'ukuran_poster' => 'A3',
        'total_harga_poster' => '150.000',
    ],
];

try {
    DB::table('metode_pembayaran')->insert($metodePembayaran);
    echo "Metode pembayaran berhasil ditambahkan.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Verify data was inserted
$count = DB::table('metode_pembayaran')->count();
echo "Total metode pembayaran: {$count}\n"; 