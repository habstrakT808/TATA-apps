<?php
namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\Editor;
use App\Models\RevisiEditor;
use Carbon\Carbon;

class PesananController extends Controller
{
    private function dirPath($idPesanan){
        if(env('APP_ENV', 'local') == 'local'){
            return public_path('assets3/img/pesanan/' . $idPesanan);
        }else{
            $path = env('PUBLIC_PATH', '/../public_html');
            return base_path($path == '/../public_html' ? $path : '/../public_html') .'/assets3/img/pesanan/' . $idPesanan;
        }
    }
    /**
     * ADMIN: Update pesanan status
     */
    public function updateStatus(Request $request){
        try {
            $validator = Validator::make($request->only('id_pesanan', 'status_pesanan'), [
                'id_pesanan' => 'required',
                'status_pesanan' => 'required|in:menunggu_editor,selesai,dibatalkan'
            ], [
                'id_pesanan.required' => 'ID pesanan harus diisi',
                'status_pesanan.required' => 'Status pesanan harus diisi',
                'status_pesanan.in' => 'Status pesanan harus diantara menunggu_editor, selesai, dibatalkan',
            ]);

            if ($validator->fails()) {
                $errors = [];
                foreach ($validator->errors()->toArray() as $field => $errorMessages){
                    $errors[$field] = $errorMessages[0];
                    break;
                }
                return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
            }

            $pesanan = Pesanan::join('transaksi', 'pesanan.id_pesanan', '=', 'transaksi.id_pesanan')
                ->where('pesanan.uuid', $request->input('id_pesanan'))
                ->first();

            if (!$pesanan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pesanan tidak ditemukan'
                ], 404);
            }
            if(in_array($pesanan->status_pesanan, ['pending', 'dibatalkan', 'selesai'])){
                switch($pesanan->status_pesanan){
                    case 'pending':
                        $message = 'masih dalam proses pembayaran';
                        break;
                    case 'dibatalkan':
                        $message = 'sudah dibatalkan';
                        break;
                    case 'selesai':
                        $message = 'sudah selesai final';
                        break;
                }
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pesanan ' . $message
                ], 400);
            }
            if($pesanan->status_transaksi != 'lunas'){
                switch($pesanan->status_transaksi){
                    case 'belum_bayar':
                        $message = 'belum dibayar';
                        break;
                    case 'menunggu_konfirmasi':
                        $message = 'masih menunggu konfirmasi';
                        break;
                    case 'dibatalkan':
                        $message = 'sudah dibatalkan';
                        break;
                    case 'expired':
                        $message = 'sudah kadaluarsa';
                        break;
                }
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaksi ' . $message
                ], 400);
            }
            $updateData = ['status_pesanan' => $request->input('status_pesanan')];
            // Set timestamps based on status
            switch ($request->input('status_pesanan')) {
                case 'menunggu_editor':
                    if($pesanan->status_pesanan == 'menunggu_editor'){
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Pesanan sudah dalam status menunggu editor'
                        ], 400);
                    }
                    $message = 'berhasil diubah ke status menunggu editor';
                    break;
                case 'selesai':
                    if($pesanan->status_pesanan == 'selesai'){
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Pesanan sudah dalam status selesai final'
                        ], 400);
                    }
                    $message = 'berhasil diselesaikan secara final';
                    break;
                case 'dibatalkan':
                    $message = 'berhasil dibatalkan';
                    break;
            }
            $pesanan->update($updateData);
            return response()->json([
                'status' => 'success',
                'message' => 'Status pesanan ' . $message,
                'data' => $pesanan->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengupdate status: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * ADMIN: Delete pesanan
     */
    public function deletePesanan(Request $request)
    {
        try {
            $validator = Validator::make($request->only('id_pesanan'), [
                'id_pesanan' => 'required|exists:pesanan,uuid',
            ], [
                'id_pesanan.required' => 'ID pesanan harus diisi',
                'id_pesanan.exists' => 'ID pesanan tidak ditemukan'
            ]);
            if ($validator->fails()) {
                $errors = [];
                foreach ($validator->errors()->toArray() as $field => $errorMessages){
                    $errors[$field] = $errorMessages[0];
                    break;
                }
                return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
            }
            $pesanan = Pesanan::where('uuid', $request->input('id_pesanan'))->first();
            if (!$pesanan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pesanan tidak ditemukan'
                ], 404);
            }
            if (in_array($pesanan->status_pesanan, ['dikerjakan', 'revisi', 'menunggu_review', 'selesai'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pesanan yang sedang dikerjakan, revisi, menunggu review, atau selesai tidak dapat dihapus'
                ], 422);
            }
            $pesanan->fromCatatanPesanan()->delete();
            $pesanan->revisions()->delete();
            $pesanan->fromTransaksi()->delete();
            $dirPath = $this->dirPath($pesanan->uuid);
            if (file_exists($dirPath) && is_dir($dirPath)) {
                $files = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($dirPath, \RecursiveDirectoryIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::CHILD_FIRST
                );
                foreach ($files as $file) {
                    if ($file->isDir()) {
                        rmdir($file->getRealPath());
                    } else {
                        unlink($file->getRealPath());
                    }
                }
                rmdir($dirPath);
            }
            $pesanan->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Pesanan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus pesanan: ' . $e->getMessage()
            ], 500);
        }
    }
}