<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DashboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create users and auth
        $authIds = $this->seedAuth();
        $userIds = $this->seedUsers($authIds['user_auth_ids']);
        $editorIds = $this->seedEditors();
        
        // Create jasa and paket jasa
        $jasaIds = $this->seedJasa();
        $paketJasaIds = $this->seedPaketJasa($jasaIds);
        
        // Create pesanan for dashboard
        $this->seedPesanan($userIds, $jasaIds, $paketJasaIds, $editorIds);
    }
    
    private function seedAuth()
    {
        $user_auth_ids = [];
        
        // Create 10 user auths
        for ($i = 1; $i <= 10; $i++) {
            $id = DB::table('auth')->insertGetId([
                'email' => 'user'.$i.'@example.com',
                'password' => bcrypt('password'),
                'role' => 'user',
            ]);
            
            $user_auth_ids[] = $id;
        }
        
        return [
            'user_auth_ids' => $user_auth_ids
        ];
    }
    
    private function seedUsers($authIds)
    {
        $userIds = [];
        $names = [
            'Andi Susanto',
            'Budi Santoso',
            'Citra Dewi',
            'Dewi Lestari',
            'Eko Prasetyo',
            'Fira Handayani',
            'Galih Pratama',
            'Hana Wulandari',
            'Irfan Hakim',
            'Joko Widodo'
        ];
        
        foreach ($authIds as $key => $authId) {
            $id = DB::table('users')->insertGetId([
                'uuid' => Str::uuid(),
                'nama_user' => $names[$key],
                'jenis_kelamin' => rand(0, 1) ? 'laki-laki' : 'perempuan',
                'no_telpon' => '08' . rand(1000000000, 9999999999),
                'alamat' => 'Jl. Contoh No. ' . ($key + 1) . ', Jakarta',
                'foto' => null,
                'email_verified_at' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'id_auth' => $authId,
            ]);
            
            $userIds[] = $id;
        }
        
        return $userIds;
    }
    
    private function seedEditors()
    {
        $editorIds = [];
        $names = [
            'Dimas Editor',
            'Rina Designer',
            'Tono Creative',
        ];
        
        foreach ($names as $key => $name) {
            $id = DB::table('editor')->insertGetId([
                'uuid' => Str::uuid(),
                'nama_editor' => $name,
                'email' => 'editor' . ($key + 1) . '@tata.com',
                'jenis_kelamin' => rand(0, 1) ? 'laki-laki' : 'perempuan',
                'no_telpon' => '08' . rand(1000000000, 9999999999),
            ]);
            
            $editorIds[] = $id;
        }
        
        return $editorIds;
    }
    
    private function seedJasa()
    {
        $categories = ['logo', 'banner', 'poster'];
        $descriptions = [
            'Jasa pembuatan logo profesional untuk kebutuhan bisnis dan personal',
            'Jasa pembuatan banner untuk kebutuhan promosi dan dekorasi',
            'Jasa pembuatan poster untuk keperluan promosi dan acara'
        ];
        
        $jasaIds = [];
        
        foreach ($categories as $key => $category) {
            $id = DB::table('jasa')->insertGetId([
                'uuid' => Str::uuid(),
                'kategori' => $category,
                'deskripsi_jasa' => $descriptions[$key],
            ]);
            
            $jasaIds[$category] = $id;
        }
        
        return $jasaIds;
    }
    
    private function seedPaketJasa($jasaIds)
    {
        $classes = ['basic', 'standard', 'premium'];
        $paketJasaIds = [];
        
        foreach ($jasaIds as $kategori => $jasaId) {
            foreach ($classes as $class) {
                $harga = 0;
                $revisi = 0;
                $waktu = '';
                $deskripsi = '';
                
                switch ($class) {
                    case 'basic':
                        $harga = $kategori == 'logo' ? 50000 : ($kategori == 'poster' ? 75000 : 100000);
                        $revisi = 1;
                        $waktu = '3 hari';
                        $deskripsi = 'Paket basic untuk ' . $kategori . ' dengan 1x revisi';
                        break;
                    case 'standard':
                        $harga = $kategori == 'logo' ? 100000 : ($kategori == 'poster' ? 150000 : 200000);
                        $revisi = 3;
                        $waktu = '5 hari';
                        $deskripsi = 'Paket standard untuk ' . $kategori . ' dengan 3x revisi';
                        break;
                    case 'premium':
                        $harga = $kategori == 'logo' ? 200000 : ($kategori == 'poster' ? 300000 : 400000);
                        $revisi = 5;
                        $waktu = '7 hari';
                        $deskripsi = 'Paket premium untuk ' . $kategori . ' dengan 5x revisi dan prioritas';
                        break;
                }
                
                $id = DB::table('paket_jasa')->insertGetId([
                    'kelas_jasa' => $class,
                    'deskripsi_singkat' => $deskripsi,
                    'harga_paket_jasa' => $harga,
                    'waktu_pengerjaan' => $waktu,
                    'maksimal_revisi' => $revisi,
                    'id_jasa' => $jasaId,
                ]);
                
                $paketJasaIds[$kategori . '_' . $class] = $id;
            }
        }
        
        return $paketJasaIds;
    }
    
    private function seedPesanan($userIds, $jasaIds, $paketJasaIds, $editorIds)
    {
        $statuses = ['pending', 'diproses', 'menunggu_editor', 'dikerjakan', 'revisi', 'menunggu_review', 'selesai', 'dibatalkan'];
        $categories = ['logo', 'banner', 'poster'];
        $classes = ['basic', 'standard', 'premium'];
        
        // Generate 50 pesanan with varying status
        $now = Carbon::now();
        $startOfYear = Carbon::createFromDate($now->year, 1, 1);
        
        for ($i = 0; $i < 50; $i++) {
            $userId = $userIds[array_rand($userIds)];
            $category = $categories[array_rand($categories)];
            $class = $classes[array_rand($classes)];
            $status = $statuses[array_rand($statuses)];
            
            $jasaId = $jasaIds[$category];
            $paketJasaId = $paketJasaIds[$category . '_' . $class];
            $editorId = $status == 'pending' || $status == 'dibatalkan' ? null : $editorIds[array_rand($editorIds)];
            
            // Generate random date within this year
            $randomDate = Carbon::createFromTimestamp(
                rand($startOfYear->timestamp, $now->timestamp)
            );
            
            // Get the paket jasa details
            $paketJasa = DB::table('paket_jasa')->where('id_paket_jasa', $paketJasaId)->first();
            
            // Parse the waktu_pengerjaan to get number of days
            $daysToAdd = intval(explode(' ', $paketJasa->waktu_pengerjaan)[0]);
            $estimasiWaktu = $randomDate->copy()->addDays($daysToAdd);
            
            $confirmedAt = in_array($status, ['diproses', 'menunggu_editor', 'dikerjakan', 'revisi', 'menunggu_review', 'selesai']) 
                ? $randomDate->copy()->addHours(rand(1, 48))
                : null;
            
            $assignedAt = in_array($status, ['dikerjakan', 'revisi', 'menunggu_review', 'selesai']) 
                ? ($confirmedAt ? $confirmedAt->copy()->addHours(rand(1, 24)) : null)
                : null;
            
            $completedAt = $status == 'selesai' 
                ? ($assignedAt ? $assignedAt->copy()->addHours(rand(24, 72)) : null)
                : null;
            
            // Generate a random description for the order
            $descriptions = [
                'logo' => [
                    "Logo untuk bisnis {$category} saya dengan tema modern",
                    "Butuh logo untuk brand {$category} dengan warna merah dan hitam",
                    "Logo untuk brand {$category} baru, konsep minimalis",
                ],
                'banner' => [
                    "Banner untuk promosi bulan ini dengan tema {$category}",
                    "Banner ukuran besar untuk acara {$category}",
                    "Banner promosi dengan tema {$category} dan warna cerah",
                ],
                'poster' => [
                    "Poster acara {$category} dengan tema yang menarik",
                    "Poster untuk promosi {$category} bulan depan",
                    "Poster ukuran A3 untuk acara {$category}",
                ]
            ];
            
            $description = $descriptions[$category][array_rand($descriptions[$category])];
            
            DB::table('pesanan')->insert([
                'uuid' => Str::uuid(),
                'deskripsi' => $description,
                'status_pesanan' => $status,
                'total_harga' => $paketJasa->harga_paket_jasa,
                'estimasi_waktu' => $estimasiWaktu,
                'maksimal_revisi' => $paketJasa->maksimal_revisi,
                'confirmed_at' => $confirmedAt,
                'assigned_at' => $assignedAt,
                'completed_at' => $completedAt,
                'created_at' => $randomDate,
                'updated_at' => $completedAt ?: ($assignedAt ?: ($confirmedAt ?: $randomDate)),
                'id_user' => $userId,
                'id_jasa' => $jasaId,
                'id_paket_jasa' => $paketJasaId,
                'id_editor' => $editorId,
            ]);
            
            // If status is 'selesai', add to the review and/or catatan pesanan tables
            if ($status == 'selesai' && rand(0, 1)) {
                // Add review
                DB::table('review')->insert([
                    'review' => ['Sangat puas dengan hasil desainnya!', 'Bagus, sesuai harapan', 'Keren banget hasilnya!'][rand(0, 2)],
                    'rating' => rand(3, 5),
                    'created_at' => $completedAt,
                    'id_pesanan' => DB::getPdo()->lastInsertId(),
                ]);
            }
        }
    }
} 