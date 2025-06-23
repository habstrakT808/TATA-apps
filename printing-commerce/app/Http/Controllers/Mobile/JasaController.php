<?php
namespace App\Http\Controllers\Mobile;
use App\Http\Controllers\Controller;
use App\Models\Jasa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\PaketJasa;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class JasaController extends Controller
{
    public function show($id = null)
    {
        // Set timeout yang sangat pendek
        set_time_limit(5);
        
        try {
            if ($id) {
                $cacheKey = "jasa_fast_{$id}";
                
                return Cache::remember($cacheKey, 300, function () use ($id) {
                    $jasa = Jasa::select('id_jasa', 'kategori')->find($id);
                    
                    if (!$jasa) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Service not found',
                            'data' => null
                        ], 404);
                    }

                    $paketJasa = PaketJasa::select(
                        'id_paket_jasa', 
                        'id_jasa', 
                        'kelas_jasa', 
                        'harga_paket_jasa', 
                        'waktu_pengerjaan', 
                        'maksimal_revisi'
                    )
                    ->where('id_jasa', $id)
                    ->orderBy('harga_paket_jasa', 'asc')
                    ->get();

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Data retrieved successfully',
                        'data' => [
                            'jasa' => $jasa,
                            'paket_jasa' => $paketJasa
                        ]
                    ], 200);
                });
            }
        } catch (\Exception $e) {
            // Return minimal error response
            return response()->json([
                'status' => 'error',
                'message' => 'Server timeout',
                'data' => null
            ], 500);
        }
    }

    public function showAll()
    {
        try {
            // Gunakan cache untuk mengurangi query database
            $cacheKey = "all_jasa_packages";
            
            $jasa = Cache::remember($cacheKey, 600, function () {
                return Jasa::with(['fromPaketJasa' => function($query) {
                    $query->select(
                        'id_paket_jasa', 
                        'id_jasa', 
                        'kelas_jasa', 
                        'harga_paket_jasa', 
                        'waktu_pengerjaan', 
                        'maksimal_revisi'
                    )->orderBy('harga_paket_jasa', 'asc');
                }])
                ->select('id_jasa', 'kategori', 'deskripsi')
                ->get();
            });
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data retrieved successfully',
                'data' => $jasa
            ], 200);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error retrieving jasa data: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve data',
                'data' => null
            ], 500);
        }
    }

    /**
     * Get service detail by ID
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDetail($id)
    {
        try {
            $cacheKey = "jasa_detail_full_{$id}";
            
            $jasa = Cache::remember($cacheKey, 300, function () use ($id) {
                return Jasa::with(['fromPaketJasa' => function($query) {
                    $query->select(
                        'id_paket_jasa', 
                        'id_jasa', 
                        'kelas_jasa', 
                        'harga_paket_jasa', 
                        'waktu_pengerjaan', 
                        'maksimal_revisi'
                    )->orderBy('harga_paket_jasa', 'asc');
                }])
                ->select('id_jasa', 'kategori', 'deskripsi', 'created_at', 'updated_at')
                ->find($id);
            });
            
            if (!$jasa) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Service not found',
                    'data' => null
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Data retrieved successfully',
                'data' => $jasa
            ], 200);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Error retrieving jasa detail: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve data',
                'data' => null
            ], 500);
        }
    }
}