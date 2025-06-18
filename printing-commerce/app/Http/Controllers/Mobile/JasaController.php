<?php
namespace App\Http\Controllers\Mobile;
use App\Http\Controllers\Controller;
use App\Models\Jasa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\PaketJasa;

class JasaController extends Controller
{
 public function show($id = null)
{
    try {
        if ($id) {
            // Ambil data jasa berdasarkan ID
            $jasa = Jasa::find($id);

            if (!$jasa) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Service not found',
                    'data' => null
                ], 404);
            }

            // Ambil semua paket jasa yang memiliki id_jasa = $id
            $paketJasa = PaketJasa::where('id_jasa', $id)->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Data retrieved successfully',
                'data' => [
                    'jasa' => $jasa,
                    'paket_jasa' => $paketJasa
                ]
            ], 200);
        } else {
            // Ambil semua jasa beserta relasi paket jasa masing-masing
            $jasa = Jasa::with('fromPaketJasa')->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Data retrieved successfully',
                'data' => $jasa
            ], 200);
        }
    } catch (\Exception $e) {
        \Log::error('Error retrieving jasa data: ' . $e->getMessage());

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to retrieve data',
            'data' => null
        ], 500);
    }
}


    public function showAll()
    {
        try {
$jasa = Jasa::with('fromPaketJasa')->get();
            
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
            $jasa = Jasa::with('fromPaketJasa')->find($id);
            
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