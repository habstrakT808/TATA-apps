<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use App\Models\CatatanPesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengerjaanController extends Controller
{
    /**
     * Get details of a pesanan by UUID
     */
    public function getDetail($id)
    {
        try {
            $pemesanan = Pemesanan::where('uuid', $id)->first();
            
            if (!$pemesanan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pesanan tidak ditemukan'
                ], 404);
            }

            // Ambil catatan pesanan terkait
            $catatanPesanan = CatatanPesanan::where('pemesanan_id', $pemesanan->id)->get();
            
            // Data yang akan dikembalikan
            $result = [
                'id' => $pemesanan->id,
                'uuid' => $pemesanan->uuid,
                'status' => $pemesanan->status,
                'total_harga' => $pemesanan->total_harga,
                'image_hasil' => $pemesanan->image_hasil,
                'revisi' => $pemesanan->revisi,
                'metode_pembayaran' => $pemesanan->metode_pembayaran,
                'paket_jasa' => $pemesanan->paket_jasa,
                'created_at' => $pemesanan->created_at,
                'updated_at' => $pemesanan->updated_at,
                'catatan_pesanan' => $catatanPesanan->map(function ($catatan) {
                    return [
                        'id' => $catatan->id,
                        'judul_catatan' => $catatan->judul_catatan,
                        'deskripsi_catatan' => $catatan->deskripsi_catatan,
                        'file_catatan' => $catatan->file_catatan,
                        'created_at' => $catatan->created_at,
                    ];
                })
            ];

            return response()->json([
                'status' => 'success',
                'data' => $result
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'trace' => $e->getTrace()
            ], 500);
        }
    }
} 