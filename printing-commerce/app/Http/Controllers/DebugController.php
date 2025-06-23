<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Pesanan;
use App\Models\User;
use App\Models\Jasa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class DebugController extends Controller
{
    public function checkReviewData()
    {
        try {
            $reviewCount = DB::table('review')->count();
            $userCount = DB::table('users')->count();
            $pesananCount = DB::table('pesanan')->count();
            
            // Check if pesanan table has uuid column
            $pesananHasUuid = DB::getSchemaBuilder()->hasColumn('pesanan', 'uuid');
            
            // Check if review table has id_pesanan column
            $reviewHasIdPesanan = DB::getSchemaBuilder()->hasColumn('review', 'id_pesanan');
            
            // Get sample data
            $sampleReview = DB::table('review')->first();
            $samplePesanan = DB::table('pesanan')->first();
            $sampleUser = DB::table('users')->first();
            
            return response()->json([
                'status' => 'success',
                'data' => [
                    'counts' => [
                        'review' => $reviewCount,
                        'users' => $userCount,
                        'pesanan' => $pesananCount,
                    ],
                    'schema' => [
                        'pesanan_has_uuid' => $pesananHasUuid,
                        'review_has_id_pesanan' => $reviewHasIdPesanan,
                    ],
                    'samples' => [
                        'review' => $sampleReview,
                        'pesanan' => $samplePesanan,
                        'user' => $sampleUser,
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    public function testReviewQuery()
    {
        try {
            // Test query with join
            $query = DB::table('review')
                ->join('pesanan', 'review.id_pesanan', '=', 'pesanan.id_pesanan')
                ->join('users', 'pesanan.id_user', '=', 'users.id_user')
                ->select(
                    'review.id_review',
                    'review.rating',
                    'review.review',
                    'users.nama_user',
                    'users.foto',
                    'pesanan.uuid as pesanan_uuid'
                )
                ->limit(5);
                
            $result = $query->get();
            
            return response()->json([
                'status' => 'success',
                'data' => $result,
                'query' => $query->toSql(),
                'bindings' => $query->getBindings()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Debug API routes
     */
    public function debugRoutes()
    {
        $routes = Route::getRoutes();
        $chatRoutes = [];
        
        // Filter for chat routes
        foreach ($routes as $route) {
            $uri = $route->uri();
            if (strpos($uri, 'chat') !== false) {
                $chatRoutes[] = [
                    'method' => implode('|', $route->methods()),
                    'uri' => $uri,
                    'name' => $route->getName(),
                    'action' => $route->getActionName(),
                ];
            }
        }
        
        return response()->json([
            'status' => 'success',
            'total_routes' => count($routes),
            'chat_routes' => $chatRoutes,
            'chat_routes_count' => count($chatRoutes),
            'create_direct_exists' => in_array('mobile/chat/create-direct', array_column($chatRoutes, 'uri'))
        ]);
    }

    /**
     * Test direct chat creation
     */
    public function testDirectChat(Request $request)
    {
        // Get the ChatController
        $chatController = app()->make(\App\Http\Controllers\Mobile\ChatController::class);
        
        // Create a test request
        $testRequest = new Request([
            'context_type' => 'product_info',
            'context_data' => [
                'id_paket_jasa' => '1',
                'id_jasa' => '1',
                'jenis_pesanan' => 'logo',
                'title' => 'basic',
                'price' => 'Rp 50000',
                'duration' => '3 hari',
                'revision' => '1x'
            ],
            'initial_message' => 'Halo, saya tertarik dengan produk ini'
        ]);
        
        try {
            // Call the createDirectChat method directly
            $response = $chatController->createDirectChat($testRequest);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Test completed',
                'controller_response' => $response->getContent(),
                'controller_status' => $response->getStatusCode()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Test failed: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
} 