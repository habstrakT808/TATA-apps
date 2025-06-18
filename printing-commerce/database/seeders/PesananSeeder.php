<?php
namespace Database\Seeders;
use App\Models\Pesanan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;
class PesananSeeder extends Seeder
{
    private static $tempFile;
    public function __construct(){
        self::$tempFile = database_path('seeders/temp/table.json');
    }
    public function run(): void
    {
        $jsonData = json_decode(file_get_contents(self::$tempFile), true);
        $idPesanans = [];
        // Sample descriptions for different services
        $descriptions = [
            'Desain logo modern untuk startup teknologi. Minimalis, profesional, dengan konsep clean dan mudah diingat.',
            'Banner promosi untuk event grand opening toko. Ukuran 3x2m, eye-catching, dengan informasi lengkap acara.',
            'Kartu nama bisnis premium dengan finishing spot UV. Design elegant dan professional untuk konsultan.',
            'Flyer promosi produk makanan sehat. A5 size, colorful design, dengan foto produk dan info nutrisi.',
            'Poster campaign awareness lingkungan. A2 size, impactful message, untuk dipasang di area publik.',
            'Desain kemasan produk kosmetik. Box packaging dengan material premium dan finishing matte.',
            'Brosur company profile untuk perusahaan konstruksi. Multi-fold, professional layout.',
            'Menu restoran dengan tema vintage. A4 folded, warm colors, mudah dibaca di lighting redup.',
            'Undangan pernikahan custom dengan tema garden party. Elegant typography dan floral elements.',
            'Kalender meja 2024 untuk corporate gift. 12 halaman dengan foto produk perusahaan.'
        ];
        // Create pesanan with realistic workflow stages
        $now = Carbon::now();
        // 1. STAGE 1: New orders (pending payment) - 15 pesanan
        for($i = 1; $i <= 15; $i++){
            $createdAt = $now->copy()->subDays(rand(1, 3));
            $idPesanan = Pesanan::insertGetId([
                'uuid' => Str::uuid(),
                'deskripsi' => $descriptions[rand(0, count($descriptions) - 1)],
                'status_pesanan' => 'pending',
                'total_harga' => [250000, 350000, 500000, 750000, 1000000][rand(0, 4)],
                'estimasi_waktu' => $createdAt->copy()->addDays(rand(3, 7)),
                'maksimal_revisi' => [3, 5, 7][rand(0, 2)],
                'id_user' => $jsonData['user'][rand(0, min(40, count($jsonData['user'])-1))],
                'id_jasa' => $jsonData['jasa'][rand(0, min(2, count($jsonData['jasa'])-1))],
                'id_paket_jasa' => $jsonData['paket_jasa'][rand(0, min(5, count($jsonData['paket_jasa'])-1))],
                'id_editor' => $jsonData['editor'][rand(0, min(5, count($jsonData['editor'])-1))],
                'created_at' => $createdAt,
                'updated_at' => $createdAt
            ]);
            $idPesanans[] = $idPesanan;
        }

        // 2. STAGE 2: Waiting for payment confirmation - 20 pesanan
        for($i = 1; $i <= 20; $i++){
            $createdAt = $now->copy()->subDays(rand(1, 5));
            $idPesanan = Pesanan::insertGetId([
                'uuid' => Str::uuid(),
                'deskripsi' => $descriptions[rand(0, count($descriptions) - 1)],
                'status_pesanan' => 'menunggu_editor',
                'total_harga' => [250000, 350000, 500000, 750000, 1000000][rand(0, 4)],
                'estimasi_waktu' => $createdAt->copy()->addDays(rand(3, 7)),
                'maksimal_revisi' => [3, 5, 7][rand(0, 2)],
                'id_user' => $jsonData['user'][rand(0, min(40, count($jsonData['user'])-1))],
                'id_jasa' => $jsonData['jasa'][rand(0, min(2, count($jsonData['jasa'])-1))],
                'id_paket_jasa' => $jsonData['paket_jasa'][rand(0, min(5, count($jsonData['paket_jasa'])-1))],
                'id_editor' => $jsonData['editor'][rand(0, min(5, count($jsonData['editor'])-1))],
                'created_at' => $createdAt,
                'updated_at' => $createdAt
            ]);
            $idPesanans[] = $idPesanan;
        }

        // 3. STAGE 3: Being worked on - 25 pesanan
        for($i = 1; $i <= 25; $i++){
            $createdAt = $now->copy()->subDays(rand(3, 10));
            $assignedAt = $createdAt->copy()->addHours(rand(2, 24));
            $idPesanan = Pesanan::insertGetId([
                'uuid' => Str::uuid(),
                'deskripsi' => $descriptions[rand(0, count($descriptions) - 1)],
                'status_pesanan' => 'dikerjakan',
                'total_harga' => [250000, 350000, 500000, 750000, 1000000][rand(0, 4)],
                'estimasi_waktu' => $createdAt->copy()->addDays(rand(3, 7)),
                'maksimal_revisi' => [3, 5, 7][rand(0, 2)],
                'confirmed_at' => $createdAt->copy()->addHours(rand(1, 12)),
                'assigned_at' => $assignedAt,
                'id_user' => $jsonData['user'][rand(0, min(40, count($jsonData['user'])-1))],
                'id_jasa' => $jsonData['jasa'][rand(0, min(2, count($jsonData['jasa'])-1))],
                'id_paket_jasa' => $jsonData['paket_jasa'][rand(0, min(5, count($jsonData['paket_jasa'])-1))],
                'id_editor' => $jsonData['editor'][rand(0, min(5, count($jsonData['editor'])-1))],
                'created_at' => $createdAt,
                'updated_at' => $assignedAt
            ]);
            $idPesanans[] = $idPesanan;
        }

        // 4. STAGE 4: In revision - 20 pesanan
        for($i = 1; $i <= 20; $i++){
            $createdAt = $now->copy()->subDays(rand(5, 15));
            $assignedAt = $createdAt->copy()->addHours(rand(2, 24));
            $revisionAt = $assignedAt->copy()->addDays(rand(1, 3));
            $idPesanan = Pesanan::insertGetId([
                'uuid' => Str::uuid(),
                'deskripsi' => $descriptions[rand(0, count($descriptions) - 1)],
                'status_pesanan' => 'revisi',
                'total_harga' => [250000, 350000, 500000, 750000, 1000000][rand(0, 4)],
                'estimasi_waktu' => $createdAt->copy()->addDays(rand(3, 7)),
                'maksimal_revisi' => [3, 5, 7][rand(0, 2)],
                'confirmed_at' => $createdAt->copy()->addHours(rand(1, 12)),
                'assigned_at' => $assignedAt,
                'id_user' => $jsonData['user'][rand(0, min(40, count($jsonData['user'])-1))],
                'id_jasa' => $jsonData['jasa'][rand(0, min(2, count($jsonData['jasa'])-1))],
                'id_paket_jasa' => $jsonData['paket_jasa'][rand(0, min(5, count($jsonData['paket_jasa'])-1))],
                'id_editor' => $jsonData['editor'][rand(0, min(5, count($jsonData['editor'])-1))],
                'created_at' => $createdAt,
                'updated_at' => $revisionAt
            ]);
            $idPesanans[] = $idPesanan;
        }

        // 5. STAGE 5: Completed - 15 pesanan
        for($i = 1; $i <= 15; $i++){
            $createdAt = $now->copy()->subDays(rand(10, 30));
            $assignedAt = $createdAt->copy()->addHours(rand(2, 24));
            $completedAt = $assignedAt->copy()->addDays(rand(2, 5));
            $idPesanan = Pesanan::insertGetId([
                'uuid' => Str::uuid(),
                'deskripsi' => $descriptions[rand(0, count($descriptions) - 1)],
                'status_pesanan' => 'selesai',
                'total_harga' => [250000, 350000, 500000, 750000, 1000000][rand(0, 4)],
                'estimasi_waktu' => $createdAt->copy()->addDays(rand(3, 7)),
                'maksimal_revisi' => [3, 5, 7][rand(0, 2)],
                'confirmed_at' => $createdAt->copy()->addHours(rand(1, 12)),
                'assigned_at' => $assignedAt,
                'completed_at' => $completedAt,
                'id_user' => $jsonData['user'][rand(0, min(40, count($jsonData['user'])-1))],
                'id_jasa' => $jsonData['jasa'][rand(0, min(2, count($jsonData['jasa'])-1))],
                'id_paket_jasa' => $jsonData['paket_jasa'][rand(0, min(5, count($jsonData['paket_jasa'])-1))],
                'id_editor' => $jsonData['editor'][rand(0, min(5, count($jsonData['editor'])-1))],
                'created_at' => $createdAt,
                'updated_at' => $completedAt
            ]);
            $idPesanans[] = $idPesanan;
        }

        // 6. STAGE 6: Cancelled orders - 5 pesanan
        for($i = 1; $i <= 5; $i++){
            $createdAt = $now->copy()->subDays(rand(7, 20));
            $idPesanan = Pesanan::insertGetId([
                'uuid' => Str::uuid(),
                'deskripsi' => $descriptions[rand(0, count($descriptions) - 1)],
                'status_pesanan' => 'dibatalkan',
                'total_harga' => [250000, 350000, 500000, 750000, 1000000][rand(0, 4)],
                'estimasi_waktu' => $createdAt->copy()->addDays(rand(3, 7)),
                'maksimal_revisi' => [3, 5, 7][rand(0, 2)],
                'id_user' => $jsonData['user'][rand(0, min(40, count($jsonData['user'])-1))],
                'id_jasa' => $jsonData['jasa'][rand(0, min(2, count($jsonData['jasa'])-1))],
                'id_paket_jasa' => $jsonData['paket_jasa'][rand(0, min(5, count($jsonData['paket_jasa'])-1))],
                'id_editor' => $jsonData['editor'][rand(0, min(5, count($jsonData['editor'])-1))],
                'created_at' => $createdAt,
                'updated_at' => $createdAt
            ]);
            $idPesanans[] = $idPesanan;
        }

        if(!isset($jsonData['pesanan'])){
            $jsonData['pesanan'] = [];
        }
        $jsonData['pesanan'] = array_merge($jsonData['pesanan'], $idPesanans);
        file_put_contents(self::$tempFile, json_encode($jsonData, JSON_PRETTY_PRINT));
    }
}