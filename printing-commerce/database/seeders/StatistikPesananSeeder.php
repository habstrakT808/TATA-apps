<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatistikPesananSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all completed orders
        $completedOrders = DB::table('pesanan')
            ->where('status_pesanan', 'selesai')
            ->whereNotNull('completed_at')
            ->get();
            
        foreach ($completedOrders as $order) {
            // Get user data
            $user = DB::table('users')->where('id_user', $order->id_user)->first();
            
            // Get jasa data
            $jasa = DB::table('jasa')->where('id_jasa', $order->id_jasa)->first();
            
            // Insert into statistik_pesanan
            DB::table('statistik_pesanan')->insert([
                'id_pesanan' => $order->id_pesanan,
                'pelanggan' => $user->nama_user,
                'jenis_jasa' => $jasa->kategori,
                'total_harga' => $order->total_harga,
                'completed_at' => $order->completed_at,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
} 