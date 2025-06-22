<?php

namespace App\Http\Controllers\Page;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Pesanan;
use App\Models\StatistikPesanan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Ambil data statistik pesanan untuk ditampilkan di dashboard
        $statistikPesanan = StatistikPesanan::select(
                'pelanggan',
                'jenis_jasa',
                'total_harga',
                'completed_at'
            )
            ->orderBy('completed_at', 'desc')
            ->limit(5)
            ->get();
            
        $list_pesanan = [];
        foreach ($statistikPesanan as $pesanan) {
            $list_pesanan[] = [
                'pelanggan' => $pesanan->pelanggan,
                'selesai_pada' => Carbon::parse($pesanan->completed_at)->format('d M Y'),
                'jenis_jasa' => ucfirst($pesanan->jenis_jasa),
                'pendapatan' => 'Rp ' . number_format($pesanan->total_harga, 0, ',', '.'),
            ];
        }
        
        // Hitung total pesanan selesai
        $total_pesanan = StatistikPesanan::count();
        
        // Ambil data penjualan per bulan untuk grafik
        $currentYear = Carbon::now()->year;
        $monthlySales = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $monthlySales[] = StatistikPesanan::whereYear('completed_at', $currentYear)
                ->whereMonth('completed_at', $month)
                ->sum('total_harga');
        }
        
        $dataShow = [
            'userAuth' => array_merge(Admin::where('id_auth', $request->user()['id_auth'])->first()->toArray(), ['role' => $request->user()['role']]),
            'list_pesanan' => $list_pesanan,
            'total_pesanan' => $total_pesanan,
            'monthly_sales' => $monthlySales,
        ];
        
        return view('page.dashboard', $dataShow);
    }
} 