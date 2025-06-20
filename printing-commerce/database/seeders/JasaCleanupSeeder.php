<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Jasa;
use App\Models\PaketJasa;
use App\Models\JasaImage;
use App\Models\Pesanan;

class JasaCleanupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus semua data jasa yang ada
        $this->cleanupAllJasa();
        
        // Tambahkan 3 jasa utama
        $this->createThreeMainJasa();
    }
    
    private function cleanupAllJasa()
    {
        // Cek apakah ada pesanan terkait dengan jasa
        $pesananCount = Pesanan::count();
        
        if ($pesananCount > 0) {
            // Jika ada pesanan, kita perlu memperbarui pesanan untuk merujuk ke jasa baru
            // Simpan id_jasa yang akan digunakan untuk pesanan
            $tempJasaIds = [];
            
            // Backup id pesanan dan kategori jasa
            $pesananData = DB::table('pesanan')
                ->join('jasa', 'pesanan.id_jasa', '=', 'jasa.id_jasa')
                ->select('pesanan.id_pesanan', 'jasa.kategori')
                ->get();
                
            // Hapus semua data jasa
            $this->truncateJasaTables();
            
            // Buat 3 jasa utama
            $jasaMap = $this->createThreeMainJasa();
            
            // Update pesanan dengan id_jasa baru berdasarkan kategori
            foreach ($pesananData as $data) {
                $kategori = $data->kategori;
                // Default ke logo jika kategori tidak ada dalam map
                $newJasaId = $jasaMap[$kategori] ?? $jasaMap['logo'];
                
                // Update pesanan
                DB::table('pesanan')
                    ->where('id_pesanan', $data->id_pesanan)
                    ->update(['id_jasa' => $newJasaId]);
            }
        } else {
            // Jika tidak ada pesanan, hapus semua jasa
            $this->truncateJasaTables();
            $this->createThreeMainJasa();
        }
    }
    
    private function truncateJasaTables()
    {
        // Hapus file gambar jasa
        $jasaImages = JasaImage::all();
        foreach ($jasaImages as $image) {
            $jasa = Jasa::find($image->id_jasa);
            if ($jasa) {
                $imagePath = public_path('assets3/img/jasa/' . $jasa->kategori . '/' . $image->image_path);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
        }
        
        // Truncate tables
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        JasaImage::truncate();
        PaketJasa::truncate();
        Jasa::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
    
    private function createThreeMainJasa()
    {
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
        
        return $jasaMap;
    }
} 