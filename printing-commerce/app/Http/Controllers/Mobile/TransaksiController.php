<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Pesanan;
use App\Models\MetodePembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class TransaksiController extends Controller
{
    private function dirPath($uuid){
        if(env('APP_ENV', 'local') == 'local'){
            return public_path('assets3/img/pesanan/' . $uuid);
        }else{
            $path = env('PUBLIC_PATH', '/../public_html');
            return base_path($path == '/../public_html' ? $path : '/../public_html') .'/assets3/img/pesanan/' . $uuid;
        }
    }
    public function getAll(Request $request){
        $transaksi = Transaksi::select('order_id')->join('pesanan', 'pesanan.id_pesanan', '=', 'transaksi.id_pesanan')
            ->where('id_user', User::select('id_user')->where('id_auth', $request->user()->id_auth)->first()->id_user)
            ->orderBy('transaksi.created_at', 'desc')
            ->get();
        return response()->json([
            'status' => 'success',
            'message' => 'Transaksi berhasil diambil',
            'data' => $transaksi
        ]);
    }
    /**
     * Get payment status label for display
     */
    private function getPaymentStatusLabel($status)
    {
        $labels = [
            'belum_bayar' => 'Belum Bayar',
            'menunggu_konfirmasi' => 'Menunggu Konfirmasi Admin',
            'lunas' => 'Lunas',
            'dibatalkan' => 'Dibatalkan', 
            'expired' => 'Kadaluarsa'
        ];
        
        return $labels[$status] ?? $status;
    }

    public function getDetail(Request $request, $order_id){
        try{
            $transaksi = Transaksi::join('pesanan', 'pesanan.id_pesanan', '=', 'transaksi.id_pesanan')
                ->where('order_id', $order_id)->where('id_user', User::select('id_user')->where('id_auth', $request->user()->id_auth)->first()->id_user)
                ->first();
            if (!$transaksi) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaksi tidak ditemukan'
                ], 404);
            }
            // Get payment status label
            $paymentStatusLabel = $this->getPaymentStatusLabel($transaksi->status_transaksi);
            return response()->json([
                'status' => 'success',
                'message' => 'Detail transaksi berhasil diambil',
                'data' => [
                    'transaction' => $transaksi,
                    'payment_status_label' => $paymentStatusLabel
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting transaction details: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil detail transaksi'
            ], 500);
        }
    }
        /**
     * Upload payment proof (Step 3 in manual payment flow)
     */
    public function uploadPaymentProof(Request $request)
    {
        $validator = Validator::make($request->only('order_id', 'bukti_pembayaran', 'catatan'), [
            'order_id' => 'required|exists:transaksi,order_id',
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'catatan' => 'nullable|string|max:200'
        ], [
            'order_id.required' => 'Order ID wajib diisi',
            'order_id.exists' => 'Transaksi tidak ditemukan',
            'bukti_pembayaran.required' => 'Bukti pembayaran wajib diupload',
            'bukti_pembayaran.image' => 'Bukti pembayaran harus berupa gambar',
            'bukti_pembayaran.mimes' => 'Format gambar harus jpeg, png, atau jpg',
            'bukti_pembayaran.max' => 'Ukuran file maksimal 2MB',
            'catatan.max' => 'Catatan maksimal 200 karakter'
        ]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->toArray() as $field => $errorMessages){
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }

        try {
            $transaksi = Transaksi::join('pesanan', 'pesanan.id_pesanan', '=', 'transaksi.id_pesanan')
                ->where('id_user', User::select('id_user')->where('id_auth', $request->user()->id_auth)->first()->id_user)
                ->where('order_id', $request->input('order_id'))
                ->first();
            
            // Check if transaction belongs to user's order
            $pesanan = Pesanan::where('id_pesanan', $transaksi->id_pesanan)
                ->where('id_user', User::select('id_user')->where('id_auth', $request->user()->id_auth)->first()->id_user)
                ->first();
                
            if (!$pesanan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaksi tidak ditemukan atau tidak memiliki akses'
                ], 404);
            }
            
            // Check if transaction is still valid (not expired)
            if (Carbon::now()->isAfter($transaksi->expired_at)) {
                $transaksi->update(['status_transaksi' => 'expired']);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaksi sudah kadaluarsa. Silakan lakukan pembayaran.'
                ], 400);
            }
            
            // Check if transaction is in valid state
            if ($transaksi->status_transaksi == 'dibatalkan') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaksi sudah dibatalkan. Silakan buat transaksi baru.'
                ], 400);
            }
            if ($transaksi->status_transaksi == 'menunggu_editor') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bukti pembayaran sudah diupload. Silakan tunggu konfirmasi admin.'
                ], 400);
            }
            if ($transaksi->status_transaksi == 'lunas') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaksi sudah lunas. Silakan tunggu konfirmasi admin.'
                ], 400);
            }
            
            // Store the uploaded file
            $file = $request->file('bukti_pembayaran');
            $fileName = $file->hashName();
            if (!file_exists($this->dirPath($transaksi->uuid) . '/bukti-pembayaran')) {
                mkdir($this->dirPath($transaksi->uuid) . '/bukti-pembayaran');
            }
            file_put_contents($this->dirPath($transaksi->uuid) . '/bukti-pembayaran/' . $fileName, file_get_contents($file));
            
            // Update transaction
            $transaksi->update([
                'bukti_pembayaran' => $fileName,
                'status_transaksi' => 'menunggu_konfirmasi',
                'waktu_pembayaran' => Carbon::now(),
                'catatan_transaksi' => $request->input('catatan')
            ]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Bukti pembayaran berhasil diupload. Menunggu konfirmasi admin.',
                'data' => [
                    'transaction' => $transaksi->fresh(),
                    'estimated_confirmation' => '1-24 jam',
                    'next_step' => 'Tunggu konfirmasi admin via notifikasi atau chat'
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Gagal mengupload bukti pembayaran: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengupload bukti pembayaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Cancel transaction (only if belum_bayar)
     */
    public function cancelTransaction(Request $request)
    {
        $validator = Validator::make($request->only('order_id', 'reason'), [
            'order_id' => 'required|exists:transaksi,order_id',
            'reason' => 'nullable|string|max:200'
        ]);

        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->toArray() as $field => $errorMessages){
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }

        try {
            $transaksi = Transaksi::where('order_id', $request->order_id)->first();
            
            // Check ownership
            $pesanan = Pesanan::where('id_pesanan', $transaksi->id_pesanan)
                ->where('id_user', User::select('id_user')->where('id_auth', $request->user()->id_auth)->first()->id_user)
                ->first();
                
            if (!$pesanan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaksi tidak ditemukan atau tidak memiliki akses'
                ], 404);
            }
            
            // Can only cancel if belum_bayar
            if ($transaksi->status_transaksi !== 'belum_bayar') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaksi tidak dapat dibatalkan pada status ini'
                ], 422);
            }
            
            $transaksi->update([
                'status_transaksi' => 'dibatalkan',
                'catatan_transaksi' => $request->reason ?? 'Dibatalkan oleh user'
            ]);
            
            // Reset pesanan status to pending for new transaction
            $pesanan->update([
                'status_pesanan' => 'pending',
            ]);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi berhasil dibatalkan'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error canceling transaction: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membatalkan transaksi'
            ], 500);
        }
    }
}