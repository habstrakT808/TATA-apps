<?php
// app/Http/Controllers/Mobile/MetodePembayaranController.php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\MetodePembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class MetodePembayaranController extends Controller
{
    /**
     * Get all metode pembayaran
     */
    public function getAll()
    {
        try {
            Log::info('🔍 Getting all payment methods');
            
            // Use the correct column names that exist in the database
            $metodePembayaran = MetodePembayaran::select([
                'uuid',
                'nama_metode_pembayaran',
                'no_metode_pembayaran',
                'deskripsi_1',
                'deskripsi_2',
                'thumbnail',
                'icon'
            ])->get();
            
            // Transform data for Flutter
            $transformedData = $metodePembayaran->map(function($item) {
                return [
                    'uuid' => $item->uuid,
                    'nama_metode_pembayaran' => $item->nama_metode_pembayaran,
                    'no_metode_pembayaran' => $item->no_metode_pembayaran,
                    'deskripsi_1' => $item->deskripsi_1,
                    'deskripsi_2' => $item->deskripsi_2,
                    'gambar' => $this->getPaymentMethodImage($item->nama_metode_pembayaran)
                ];
            });
            
            Log::info("✅ Found {$metodePembayaran->count()} payment methods");

            return response()->json([
                'status' => 'success',
                'message' => 'Data metode pembayaran berhasil diambil',
                'data' => $transformedData
            ]);
        } catch (\Exception $e) {
            Log::error('❌ Error getting payment methods: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data metode pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Map payment method name to Flutter asset path
     */
    private function getPaymentMethodImage($namaMetode)
    {
        $namaMetodeLower = strtolower($namaMetode);
        
        if (str_contains($namaMetodeLower, 'mandiri')) {
            return 'assets/images/BankMandiri.png';
        } elseif (str_contains($namaMetodeLower, 'bni')) {
            return 'assets/images/BankBNI.png';
        } elseif (str_contains($namaMetodeLower, 'ovo')) {
            return 'assets/images/OVO.png';
        } elseif (str_contains($namaMetodeLower, 'bri')) {
            return 'assets/images/BankBNI.png'; // Using BNI image for BRI temporarily
        } elseif (str_contains($namaMetodeLower, 'bca')) {
            return 'assets/images/BankBNI.png'; // Using BNI image for BCA temporarily
        } else {
            return 'assets/images/BankMandiri.png'; // Default
        }
    }

    /**
     * Get metode pembayaran by UUID
     */
    public function getDetail($uuid)
    {
        try {
            Log::info('Getting payment method detail: ' . $uuid);
            
            $metodePembayaran = MetodePembayaran::where('uuid', $uuid)->first();

            if (!$metodePembayaran) {
                Log::warning('Payment method not found: ' . $uuid);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Metode pembayaran tidak ditemukan'
                ], 404);
            }
            
            // Add image path for frontend
            $metodePembayaran->gambar = $this->getPaymentMethodImage($metodePembayaran->nama_metode_pembayaran);
            
            Log::info('Payment method found: ' . $metodePembayaran->nama_metode_pembayaran);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Detail metode pembayaran berhasil diambil',
                'data' => $metodePembayaran
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting metode pembayaran detail: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil detail metode pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }
}