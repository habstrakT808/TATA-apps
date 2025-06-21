<?php
// database/seeders/MetodePembayaranSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MetodePembayaranSeeder extends Seeder
{
    public function run(): void
    {
        $metodePembayaran = [
            [
                'uuid' => Str::uuid(),
                'nama_metode_pembayaran' => 'Bank Mandiri',
                'no_metode_pembayaran' => '1234567890',
                'deskripsi_1' => 'TATA Design Studio',
                'deskripsi_2' => 'Transfer ke rekening di atas',
                'thumbnail' => 'mandiri.png',
                'icon' => 'mandiri-icon.png',
                'bahan_poster' => 'Art Paper',
                'ukuran_poster' => 'A3',
                'total_harga_poster' => '150.000',
            ],
            [
                'uuid' => Str::uuid(),
                'nama_metode_pembayaran' => 'Bank BCA',
                'no_metode_pembayaran' => '0987654321',
                'deskripsi_1' => 'TATA Design Studio',
                'deskripsi_2' => 'Transfer ke rekening di atas',
                'thumbnail' => 'bca.png',
                'icon' => 'bca-icon.png',
                'bahan_poster' => 'Art Paper',
                'ukuran_poster' => 'A3',
                'total_harga_poster' => '150.000',
            ],
            [
                'uuid' => Str::uuid(),
                'nama_metode_pembayaran' => 'Bank BRI',
                'no_metode_pembayaran' => '1122334455',
                'deskripsi_1' => 'TATA Design Studio',
                'deskripsi_2' => 'Transfer ke rekening di atas',
                'thumbnail' => 'bri.png',
                'icon' => 'bri-icon.png',
                'bahan_poster' => 'Art Paper',
                'ukuran_poster' => 'A3',
                'total_harga_poster' => '150.000',
            ],
        ];

        DB::table('metode_pembayaran')->insert($metodePembayaran);
    }
}