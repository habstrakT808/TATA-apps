<?php
// test_create_pesanan.php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Ambil UUID metode pembayaran yang valid dari database
use Illuminate\Support\Facades\DB;
$metodePembayaran = DB::table('metode_pembayaran')->first();

if (!$metodePembayaran) {
    echo "Error: Tidak ada metode pembayaran di database!\n";
    exit(1);
}

$validUuid = $metodePembayaran->uuid;

// Buat request simulasi
$request = \Illuminate\Http\Request::create(
    '/api/mobile/pesanan/create-with-transaction',
    'POST',
    [
        'id_jasa' => 1,
        'id_paket_jasa' => 1,
        'catatan_user' => 'Test pesanan via script',
        'maksimal_revisi' => 1,
        'id_metode_pembayaran' => $validUuid
    ]
);

// Tambahkan header Authorization
$token = '85|Dmbkfbvv6tNKmTQqLNHJW7uWZ5xo1ltEUY5Kms7cddb491bf'; // Ganti dengan token yang valid
$request->headers->set('Authorization', 'Bearer ' . $token);
$request->headers->set('Accept', 'application/json');

// Jalankan request melalui kernel
$response = $app->handle($request);

// Tampilkan hasil
echo "Status Code: " . $response->getStatusCode() . "\n\n";
echo "Response Body:\n";
echo json_encode(json_decode($response->getContent()), JSON_PRETTY_PRINT);
echo "\n"; 