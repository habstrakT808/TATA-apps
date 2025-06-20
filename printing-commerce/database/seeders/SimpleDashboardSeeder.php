<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Pesanan;
use App\Models\Transaksi;
use App\Models\MetodePembayaran;
use App\Models\User;
use App\Models\Jasa;
use App\Models\PaketJasa;
use App\Models\Editor;

class SimpleDashboardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing data
        $users = User::all();
        $jasas = Jasa::all();
        $paketJasas = PaketJasa::all();
        $editors = Editor::all();
        $metodes = MetodePembayaran::all();
        
        if ($users->isEmpty() || $jasas->isEmpty() || $paketJasas->isEmpty() || $editors->isEmpty() || $metodes->isEmpty()) {
            echo "Error: Pastikan data users, jasa, paket jasa, editors, dan metode pembayaran sudah ada.\n";
            return;
        }
        
        $statuses = ['pending', 'diproses', 'menunggu_editor', 'dikerjakan', 'revisi', 'menunggu_review', 'selesai', 'dibatalkan'];
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
        
        $now = Carbon::now();
        $startOfYear = Carbon::createFromDate($now->year, 1, 1);
        
        // Generate 100 pesanan with varying status
        for ($i = 0; $i < 100; $i++) {
            $user = $users->random();
            $jasa = $jasas->random();
            $paketJasa = $paketJasas->where('id_jasa', $jasa->id_jasa)->random();
            $editor = $editors->random();
            $status = $statuses[array_rand($statuses)];
            
            // Generate random date within this year
            $randomDate = Carbon::createFromTimestamp(
                rand($startOfYear->timestamp, $now->timestamp)
            );
            
            // Parse the waktu_pengerjaan to get number of days
            $daysToAdd = 3; // Default to 3 days
            if (preg_match('/(\d+)/', $paketJasa->waktu_pengerjaan, $matches)) {
                $daysToAdd = (int) $matches[1];
            }
            
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
            
            $description = $descriptions[array_rand($descriptions)];
            
            // Create pesanan
            $pesanan = new Pesanan();
            $pesanan->uuid = Str::uuid();
            $pesanan->deskripsi = $description;
            $pesanan->status_pesanan = $status;
            $pesanan->total_harga = $paketJasa->harga_paket_jasa;
            $pesanan->estimasi_waktu = $estimasiWaktu;
            $pesanan->maksimal_revisi = $paketJasa->maksimal_revisi;
            $pesanan->confirmed_at = $confirmedAt;
            $pesanan->assigned_at = $assignedAt;
            $pesanan->completed_at = $completedAt;
            $pesanan->created_at = $randomDate;
            $pesanan->updated_at = $completedAt ?: ($assignedAt ?: ($confirmedAt ?: $randomDate));
            $pesanan->id_user = $user->id_user;
            $pesanan->id_jasa = $jasa->id_jasa;
            $pesanan->id_paket_jasa = $paketJasa->id_paket_jasa;
            $pesanan->id_editor = ($status == 'pending' || $status == 'dibatalkan') ? null : $editor->id_editor;
            $pesanan->save();
            
            // Create transaksi for non-pending and non-cancelled pesanan
            if (!in_array($status, ['pending', 'dibatalkan'])) {
                $metode = $metodes->random();
                $transaksiStatus = in_array($status, ['diproses', 'menunggu_editor']) ? 'menunggu_konfirmasi' : 'lunas';
                $waktuPembayaran = $randomDate->copy()->addHours(rand(1, 24));
                
                $transaksi = new Transaksi();
                $transaksi->order_id = 'ORD-' . $randomDate->format('Ymd') . '-' . strtoupper(Str::random(6));
                $transaksi->jumlah = $pesanan->total_harga;
                $transaksi->status_transaksi = $transaksiStatus;
                $transaksi->bukti_pembayaran = 'payment_proof_' . rand(1, 5) . '.jpg';
                $transaksi->waktu_pembayaran = $waktuPembayaran;
                $transaksi->confirmed_at = $transaksiStatus == 'lunas' ? $confirmedAt : null;
                $transaksi->catatan_transaksi = $transaksiStatus == 'lunas' ? 'Pembayaran sudah dikonfirmasi' : null;
                $transaksi->expired_at = $randomDate->copy()->addHours(24);
                $transaksi->id_metode_pembayaran = $metode->id_metode_pembayaran;
                $transaksi->id_pesanan = $pesanan->id_pesanan;
                $transaksi->created_at = $randomDate;
                $transaksi->updated_at = $transaksiStatus == 'lunas' ? $confirmedAt : $waktuPembayaran;
                $transaksi->save();
            }
        }
    }
} 