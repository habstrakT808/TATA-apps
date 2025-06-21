<?php
// app/Http/Controllers/Mobile/MetodePembayaranController.php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\MetodePembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MetodePembayaranController extends Controller
{
    /**
     * Get all metode pembayaran
     */
    public function getAll()
    {
        try {
            $metodePembayaran = MetodePembayaran::where('is_active', true)
                ->orderBy('nama_metode', 'asc')
                ->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Data metode pembayaran berhasil diambil',
                'data' => $metodePembayaran
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting metode pembayaran: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data metode pembayaran'
            ], 500);
        }
    }

    /**
     * Get metode pembayaran by UUID
     */
    public function getDetail($uuid)
    {
        try {
            $metodePembayaran = MetodePembayaran::where('uuid', $uuid)
                ->where('is_active', true)
                ->first();

            if (!$metodePembayaran) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Metode pembayaran tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Detail metode pembayaran berhasil diambil',
                'data' => $metodePembayaran
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting metode pembayaran detail: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil detail metode pembayaran'
            ], 500);
        }
    }
}