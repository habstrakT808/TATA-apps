<?php

namespace Database\Seeders;

use App\Models\PaketJasa;
use App\Models\Jasa;
use Illuminate\Database\Seeder;

class BannerPaketJasaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cek jasa banner
        $jasa = Jasa::where('kategori', 'banner')->orWhere('kategori', 'Banner')->first();
        
        if (!$jasa) {
            $this->command->error('Jasa banner tidak ditemukan!');
            return;
        }
        
        $this->command->info('Menambahkan paket jasa untuk Banner (ID: ' . $jasa->id_jasa . ')');
        
        // Hapus paket-paket yang mungkin sudah ada
        PaketJasa::where('id_jasa', $jasa->id_jasa)->delete();
        
        // Tambahkan paket jasa untuk Banner
        PaketJasa::create([
            'kelas_jasa' => 'basic',
            'deskripsi_singkat' => 'Paket basic untuk desain banner sederhana',
            'harga_paket_jasa' => 150000,
            'waktu_pengerjaan' => '3 hari',
            'maksimal_revisi' => 2,
            'id_jasa' => $jasa->id_jasa,
        ]);
        
        PaketJasa::create([
            'kelas_jasa' => 'standard',
            'deskripsi_singkat' => 'Paket standard untuk desain banner profesional',
            'harga_paket_jasa' => 300000,
            'waktu_pengerjaan' => '5 hari',
            'maksimal_revisi' => 5,
            'id_jasa' => $jasa->id_jasa,
        ]);
        
        PaketJasa::create([
            'kelas_jasa' => 'premium',
            'deskripsi_singkat' => 'Paket premium untuk desain banner eksklusif',
            'harga_paket_jasa' => 450000,
            'waktu_pengerjaan' => '7 hari',
            'maksimal_revisi' => 10,
            'id_jasa' => $jasa->id_jasa,
        ]);
        
        $this->command->info('Paket jasa untuk Banner berhasil ditambahkan');
    }
} 