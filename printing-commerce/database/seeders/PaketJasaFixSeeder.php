<?php

namespace Database\Seeders;

use App\Models\PaketJasa;
use App\Models\Jasa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaketJasaFixSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Mulai memperbaiki semua paket jasa...');
        
        // Menghapus semua paket jasa yang ada untuk memastikan tidak ada duplikasi
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('paket_jasa')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->command->info('Semua paket jasa telah dihapus, akan membuat baru...');
        
        // Ambil semua jasa
        $jasaList = Jasa::all();
        
        foreach ($jasaList as $jasa) {
            $this->command->info("Menambahkan paket jasa untuk {$jasa->kategori} (ID: {$jasa->id_jasa})");
            
            // Tambahkan paket jasa untuk setiap jasa
            PaketJasa::create([
                'kelas_jasa' => 'basic',
                'deskripsi_singkat' => "Paket basic untuk desain {$jasa->kategori} sederhana",
                'harga_paket_jasa' => 150000,
                'waktu_pengerjaan' => '3 hari',
                'maksimal_revisi' => 2,
                'id_jasa' => $jasa->id_jasa,
            ]);
            
            PaketJasa::create([
                'kelas_jasa' => 'standard',
                'deskripsi_singkat' => "Paket standard untuk desain {$jasa->kategori} profesional",
                'harga_paket_jasa' => 300000,
                'waktu_pengerjaan' => '5 hari',
                'maksimal_revisi' => 5,
                'id_jasa' => $jasa->id_jasa,
            ]);
            
            PaketJasa::create([
                'kelas_jasa' => 'premium',
                'deskripsi_singkat' => "Paket premium untuk desain {$jasa->kategori} eksklusif",
                'harga_paket_jasa' => 450000,
                'waktu_pengerjaan' => '7 hari',
                'maksimal_revisi' => 10,
                'id_jasa' => $jasa->id_jasa,
            ]);
        }
        
        // Pastikan semua jasa memiliki paket
        $paketCount = PaketJasa::count();
        $jasaCount = Jasa::count() * 3; // Karena ada 3 paket untuk setiap jasa
        
        if ($paketCount == $jasaCount) {
            $this->command->info('Semua paket jasa telah berhasil ditambahkan!');
        } else {
            $this->command->error('Ada beberapa paket jasa yang gagal ditambahkan!');
        }
    }
} 