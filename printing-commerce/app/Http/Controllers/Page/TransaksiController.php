<?php
namespace App\Http\Controllers\Page;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Http\Controllers\UtilityController;
use App\Models\Transaksi;
use App\Models\Pesanan;
use App\Models\MetodePembayaran;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
class TransaksiController extends Controller
{
    public function showAll(Request $request){
        $dataShow = [
            'headerData' => UtilityController::getHeaderData(),
            'userAuth' => array_merge(Admin::where('id_auth', $request->user()['id_auth'])->first()->toArray(), ['role' => $request->user()['role']]),
            'transaksiData' => Transaksi::select('order_id', 'users.nama_user', 'metode_pembayaran.nama_metode_pembayaran', 'transaksi.status_transaksi')
                ->join('pesanan', 'transaksi.id_pesanan', '=', 'pesanan.id_pesanan')
                ->join('users', 'pesanan.id_user', '=', 'users.id_user')
                ->join('metode_pembayaran', 'transaksi.id_metode_pembayaran', '=', 'metode_pembayaran.id_metode_pembayaran')
                ->orderBy('transaksi.status_transaksi', 'asc')
                ->orderBy('transaksi.created_at', 'desc')
                ->get()
                ->map(function($item) {
                    $item->status_transaksi = ucwords(str_replace('_', ' ', $item->status_transaksi));
                    return $item;
                }),
            'totalPending' => Transaksi::where('status_transaksi', 'menunggu_konfirmasi')->count(),
            'totalCompleted' => Transaksi::where('status_transaksi', 'lunas')->count(),
        ];
        return view('page.transaksi.data',$dataShow);
    }
    public function showDetail(Request $request, $orderId){
        $transaksi = Transaksi::select('order_id', 'transaksi.status_transaksi', 'bukti_pembayaran', 'waktu_pembayaran', 'expired_at', 'transaksi.created_at', 'transaksi.updated_at', 'users.nama_user', 'metode_pembayaran.nama_metode_pembayaran')
            ->where('order_id', $orderId)
            ->join('pesanan', 'transaksi.id_pesanan', '=', 'pesanan.id_pesanan')
            ->join('users', 'pesanan.id_user', '=', 'users.id_user')
            ->join('metode_pembayaran', 'transaksi.id_metode_pembayaran', '=', 'metode_pembayaran.id_metode_pembayaran')
            ->first();
        // echo json_encode($transaksi);
        // exit();
        if (!$transaksi) {
            return redirect('/transaksi')->with('error', 'Data Transaksi tidak ditemukan');
        }
        // Get order and user information
        $pesanan = $transaksi->toPesanan;
        $user = null;
        if ($pesanan && $pesanan->toUser) {
            $user = $pesanan->toUser;
        }
        
        $dataShow = [
            'headerData' => UtilityController::getHeaderData(),
            'userAuth' => array_merge(Admin::where('id_auth', $request->user()['id_auth'])->first()->toArray(), ['role' => $request->user()['role']]),
            'transaksiData' => $transaksi,
        ];
        // echo json_encode($dataShow);
        // exit();
        return view('page.transaksi.detail', $dataShow);
    }

    // public function showTambah(Request $request){
    //     $dataShow = [
    //         'headerData' => UtilityController::getHeaderData(),
    //         'userAuth' => array_merge(Admin::where('id_auth', $request->user()['id_auth'])->first()->toArray(), ['role' => $request->user()['role']]),
    //         'metodePembayaran' => MetodePembayaran::all(),
    //         'pesanan' => Pesanan::where('status_pembayaran', 'belum_bayar')->get(),
    //     ];
        
    //     return view('page.transaksi.tambah', $dataShow);
    // }
    /**
     * Admin: Get pending payments for review
     */
    public function getPendingPayments(Request $request)
    {
        try {
            $limit = $request->query('limit', 20);
            
            $pendingPayments = Transaksi::with(['toPesanan.toUser.toAuth', 'toMetodePembayaran'])
                ->where('status_transaksi', 'menunggu_konfirmasi')
                ->whereNotNull('bukti_pembayaran')
                ->orderBy('waktu_pembayaran', 'asc')
                ->paginate($limit);

            return response()->json([
                'status' => 'success',
                'message' => 'Daftar pembayaran pending berhasil diambil',
                'data' => $pendingPayments
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting pending payments: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil daftar pembayaran pending'
            ], 500);
        }
    }
    /**
     * Get user transactions with filtering
     */
    public function getUserTransactions(Request $request)
    {
        try {
            $status = $request->query('status');
            $limit = $request->query('limit', 10);
            
            $query = Transaksi::with(['toMetodePembayaran', 'toPesanan.toJasa'])
                ->whereHas('toPesanan', function ($query) use ($request) {
                    $query->where('id_user', User::select('id_user')->where('id_auth', $request->user()->id_auth)->first()->id_user);
                });
            
            if ($status && in_array($status, ['belum_bayar', 'menunggu_konfirmasi', 'lunas', 'dibatalkan', 'expired'])) {
                $query->where('status_transaksi', $status);
            }
            
            $transactions = $query->orderBy('created_at', 'desc')
                ->paginate($limit);
                
            return response()->json([
                'status' => 'success',
                'message' => 'Riwayat transaksi berhasil diambil',
                'data' => $transactions
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting user transactions: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil riwayat transaksi'
            ], 500);
        }
    }
    public function showReports(Request $request){
        // Get filter parameters
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $status = $request->input('status', 'all');
        
        // Base query
        $query = Transaksi::with(['toMetodePembayaran', 'toPesanan']);
        
        // Apply filters
        if ($status != 'all') {
            $query->where('status_transaksi', $status);
        }
        
        $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        
        // Get transactions
        $transactions = $query->orderBy('created_at', 'desc')->get();
        
        // Calculate summary stats
        $summary = [
            'total_count' => $transactions->count(),
            'total_amount' => $transactions->sum('jumlah'),
            'confirmed_count' => $transactions->where('status_transaksi', 'lunas')->count(),
            'confirmed_amount' => $transactions->where('status_transaksi', 'lunas')->sum('jumlah'),
            'pending_count' => $transactions->where('status_transaksi', 'menunggu_konfirmasi')->count(),
            'pending_amount' => $transactions->where('status_transaksi', 'menunggu_konfirmasi')->sum('jumlah'),
            'unpaid_count' => $transactions->where('status_transaksi', 'belum_bayar')->count(),
            'unpaid_amount' => $transactions->where('status_transaksi', 'belum_bayar')->sum('jumlah'),
        ];
        
        $dataShow = [
            'headerData' => UtilityController::getHeaderData(),
            'userAuth' => array_merge(Admin::where('id_auth', $request->user()['id_auth'])->first()->toArray(), ['role' => $request->user()['role']]),
            'transactions' => $transactions,
            'summary' => $summary,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'status' => $status,
        ];
        
        return view('page.transaksi.reports', $dataShow);
    }
    public function exportTransactions(Request $request)
    {
        try {
            $fileName = 'transactions_' . date('Y-m-d') . '.xlsx';
            
            // Filter parameters
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $status = $request->input('status');
            
            return Excel::download(new TransaksiExport($startDate, $endDate, $status), $fileName);
            
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to export transactions: ' . $e->getMessage());
        }
    }
}