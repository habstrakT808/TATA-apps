<?php

// Load Laravel framework
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Jasa;
use App\Models\PaketJasa;
use App\Models\JasaImage;
use App\Models\Pesanan;

echo "Starting jasa cleanup...\n";

// Hapus semua jasa secara paksa
echo "Removing all existing jasa...\n";

// Hapus semua jasa secara paksa
DB::statement('SET FOREIGN_KEY_CHECKS=0');

// Backup pesanan yang ada
$pesananData = [];
if (DB::table('pesanan')->count() > 0) {
    $pesananData = DB::table('pesanan')
        ->join('jasa', 'pesanan.id_jasa', '=', 'jasa.id_jasa')
        ->select('pesanan.id_pesanan', 'jasa.kategori')
        ->get()
        ->toArray();
}

// Hapus semua file gambar jasa
$jasaDirs = ['logo', 'banner', 'poster'];
foreach ($jasaDirs as $dir) {
    $targetDir = public_path('assets3/img/jasa/' . $dir);
    if (file_exists($targetDir)) {
        $files = glob($targetDir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}

// Truncate tables - menggunakan model untuk mendapatkan nama tabel yang benar
JasaImage::truncate();
PaketJasa::truncate();
Jasa::truncate();

DB::statement('SET FOREIGN_KEY_CHECKS=1');

// Tambahkan 3 jasa utama
echo "Creating exactly 3 main jasa...\n";

$jasaMap = [];

$jasaData = [
    [
        'kategori' => 'logo',
        'deskripsi_jasa' => 'Jasa pembuatan logo profesional untuk kebutuhan bisnis dan personal',
        'display_name' => 'Desain Logo',
        'paket' => [
            'basic' => [
                'harga' => 50000,
                'deskripsi' => 'Paket basic untuk desain logo dengan 1x revisi',
                'waktu' => '3 hari',
                'revisi' => 1
            ],
            'standard' => [
                'harga' => 100000,
                'deskripsi' => 'Paket standard untuk desain logo dengan 3x revisi',
                'waktu' => '5 hari',
                'revisi' => 3
            ],
            'premium' => [
                'harga' => 200000,
                'deskripsi' => 'Paket premium untuk desain logo dengan 5x revisi dan prioritas',
                'waktu' => '7 hari',
                'revisi' => 5
            ]
        ]
    ],
    [
        'kategori' => 'banner',
        'deskripsi_jasa' => 'Jasa pembuatan banner untuk kebutuhan promosi dan dekorasi',
        'display_name' => 'Desain Banner',
        'paket' => [
            'basic' => [
                'harga' => 100000,
                'deskripsi' => 'Paket basic untuk desain banner dengan 1x revisi',
                'waktu' => '3 hari',
                'revisi' => 1
            ],
            'standard' => [
                'harga' => 200000,
                'deskripsi' => 'Paket standard untuk desain banner dengan 3x revisi',
                'waktu' => '5 hari',
                'revisi' => 3
            ],
            'premium' => [
                'harga' => 400000,
                'deskripsi' => 'Paket premium untuk desain banner dengan 5x revisi dan prioritas',
                'waktu' => '7 hari',
                'revisi' => 5
            ]
        ]
    ],
    [
        'kategori' => 'poster',
        'deskripsi_jasa' => 'Jasa pembuatan poster untuk keperluan promosi dan acara',
        'display_name' => 'Desain Poster',
        'paket' => [
            'basic' => [
                'harga' => 75000,
                'deskripsi' => 'Paket basic untuk desain poster dengan 1x revisi',
                'waktu' => '3 hari',
                'revisi' => 1
            ],
            'standard' => [
                'harga' => 150000,
                'deskripsi' => 'Paket standard untuk desain poster dengan 3x revisi',
                'waktu' => '5 hari',
                'revisi' => 3
            ],
            'premium' => [
                'harga' => 300000,
                'deskripsi' => 'Paket premium untuk desain poster dengan 5x revisi dan prioritas',
                'waktu' => '7 hari',
                'revisi' => 5
            ]
        ]
    ]
];

foreach ($jasaData as $data) {
    // Buat jasa
    $jasa = Jasa::create([
        'uuid' => Str::uuid(),
        'kategori' => $data['kategori'],
        'deskripsi_jasa' => $data['deskripsi_jasa']
    ]);
    
    // Simpan id jasa untuk mapping
    $jasaMap[$data['kategori']] = $jasa->id_jasa;
    
    // Buat paket untuk setiap jasa
    foreach ($data['paket'] as $kelas => $paket) {
        PaketJasa::create([
            'kelas_jasa' => $kelas,
            'deskripsi_singkat' => $paket['deskripsi'],
            'harga_paket_jasa' => $paket['harga'],
            'waktu_pengerjaan' => $paket['waktu'],
            'maksimal_revisi' => $paket['revisi'],
            'id_jasa' => $jasa->id_jasa
        ]);
    }
    
    // Pastikan direktori untuk gambar ada
    $targetDir = public_path('assets3/img/jasa/' . $data['kategori']);
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    // Tambahkan placeholder image
    $placeholderImage = 'placeholder_' . $data['kategori'] . '.jpg';
    $placeholderPath = public_path('assets3/img/jasa/' . $data['kategori'] . '/' . $placeholderImage);
    
    // Copy placeholder image dari assets/img jika ada
    $sourcePath = public_path('assets/img/placeholder_' . $data['kategori'] . '.jpg');
    if (file_exists($sourcePath)) {
        copy($sourcePath, $placeholderPath);
    } else {
        // Jika tidak ada, buat file placeholder kosong
        file_put_contents($placeholderPath, '');
    }
    
    // Tambahkan entry di database
    JasaImage::create([
        'image_path' => $placeholderImage,
        'id_jasa' => $jasa->id_jasa
    ]);
}

// Update pesanan jika ada
$pesananCount = Pesanan::count();
if ($pesananCount > 0) {
    // Default semua pesanan ke jasa logo
    DB::table('pesanan')->update(['id_jasa' => $jasaMap['logo']]);
}

echo "Jasa cleanup completed successfully!\n";
echo "Total jasa: " . Jasa::count() . "\n";

// Tampilkan semua jasa
$allJasa = Jasa::all();
foreach ($allJasa as $jasa) {
    echo "ID: " . $jasa->id_jasa . ", Kategori: " . $jasa->kategori . ", UUID: " . $jasa->uuid . "\n";
}

echo "Done!\n"; 