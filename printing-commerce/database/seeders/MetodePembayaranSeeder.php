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
            ]
        ];

        DB::table('metode_pembayaran')->insert($metodePembayaran);
    }
}