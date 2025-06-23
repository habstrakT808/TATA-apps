<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PublicReviewController extends Controller
{
    public function getPublicReviews()
    {
        try {
            Log::info('Starting to fetch real reviews from database');
            
            // Coba ambil review asli dari database terlebih dahulu
            $realReviews = $this->getRealReviewsFromDatabase();
            
            if ($realReviews->isNotEmpty()) {
                Log::info("Found {$realReviews->count()} real reviews from database");
                
                $transformedReviews = $realReviews->map(function ($review) {
                    return [
                        'id' => (string) $review->id,
                        'name' => $review->name ?: 'Pengguna',
                        'avatar_url' => $review->avatar_url, // Akan null jika tidak ada foto
                        'rating' => (int) $review->rating,
                        'feedback' => $review->feedback,
                        'service' => $review->service ?: 'Desain',
                        'order_uuid' => $review->order_uuid,
                        'completion_date' => $review->completion_date,
                        'review_date' => $review->review_date
                    ];
                });

                return response()->json([
                    'status' => 'success',
                    'data' => $transformedReviews,
                    'message' => 'Real user reviews fetched successfully',
                    'source' => 'database'
                ]);
            }
            
            Log::info('No real reviews found, checking if any reviews exist at all');
            
            // Jika tidak ada review real, cek apakah ada review sama sekali
            $anyReviews = DB::table('review')->count();
            
            if ($anyReviews > 0) {
                Log::info("Found $anyReviews total reviews but none meet criteria");
                
                // Ada review tapi tidak memenuhi kriteria, ambil yang terbaik yang ada
                $basicReviews = $this->getBasicReviews();
                
                if ($basicReviews->isNotEmpty()) {
                    $transformedReviews = $basicReviews->map(function ($review) {
                        return [
                            'id' => (string) $review->id,
                            'name' => $review->name ?: 'Pengguna',
                            'avatar_url' => $review->avatar_url,
                            'rating' => (int) $review->rating,
                            'feedback' => $review->feedback,
                            'service' => 'Desain',
                            'order_uuid' => null,
                            'completion_date' => $review->review_date,
                            'review_date' => $review->review_date
                        ];
                    });

                    return response()->json([
                        'status' => 'success',
                        'data' => $transformedReviews,
                        'message' => 'Basic user reviews fetched successfully',
                        'source' => 'basic_database'
                    ]);
                }
            }
            
            Log::info('No reviews found in database at all, returning empty array');
            
            // Jika benar-benar tidak ada review, return array kosong
            return response()->json([
                'status' => 'success',
                'data' => [],
                'message' => 'No reviews available yet',
                'source' => 'empty'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching reviews: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'status' => 'success',
                'data' => [],
                'message' => 'Error fetching reviews, returning empty',
                'source' => 'error'
            ]);
        }
    }
    
    private function getRealReviewsFromDatabase()
    {
        try {
            // Query dengan join lengkap untuk review asli
            return DB::table('review')
                ->join('pesanan', 'review.id_pesanan', '=', 'pesanan.id_pesanan')
                ->join('users', 'pesanan.id_user', '=', 'users.id_user')
                ->leftJoin('jasa', 'pesanan.id_jasa', '=', 'jasa.id_jasa')
                ->select(
                    'review.id_review as id',
                    'users.nama_user as name',
                    'users.foto as avatar_url',
                    'review.rating',
                    'review.review as feedback',
                    'jasa.kategori as service',
                    'pesanan.uuid as order_uuid',
                    'pesanan.completed_at as completion_date',
                    'review.created_at as review_date'
                )
                ->where('review.rating', '>=', 3) // Rating minimal 3
                ->whereNotNull('users.nama_user') // Pastikan ada nama user
                ->orderBy('review.created_at', 'desc')
                ->limit(10)
                ->get();
                
        } catch (\Exception $e) {
            Log::error('Error in getRealReviewsFromDatabase: ' . $e->getMessage());
            return collect(); // Return empty collection
        }
    }
    
    private function getBasicReviews()
    {
        try {
            // Query sederhana hanya dari tabel review dan users
            return DB::table('review')
                ->join('pesanan', 'review.id_pesanan', '=', 'pesanan.id_pesanan')
                ->join('users', 'pesanan.id_user', '=', 'users.id_user')
                ->select(
                    'review.id_review as id',
                    'users.nama_user as name',
                    'users.foto as avatar_url',
                    'review.rating',
                    'review.review as feedback',
                    'review.created_at as review_date'
                )
                ->where('review.rating', '>=', 3)
                ->whereNotNull('users.nama_user')
                ->orderBy('review.created_at', 'desc')
                ->limit(10)
                ->get();
                
        } catch (\Exception $e) {
            Log::error('Error in getBasicReviews: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get reviews by service type
     */
    public function getReviewsByService($serviceId)
    {
        try {
            $reviews = DB::table('review')
                ->join('pesanan', 'review.id_pesanan', '=', 'pesanan.id_pesanan')
                ->join('users', 'pesanan.id_user', '=', 'users.id_user')
                ->join('jasa', 'pesanan.id_jasa', '=', 'jasa.id_jasa')
                ->select(
                    'review.id_review as id',
                    'users.nama_user as name',
                    'users.foto as avatar_url',
                    'review.rating',
                    'review.review as feedback',
                    'jasa.kategori as service',
                    'review.created_at as review_date'
                )
                ->where('jasa.id_jasa', $serviceId)
                ->where('review.rating', '>=', 3)
                ->whereNotNull('pesanan.completed_at')
                ->orderBy('review.created_at', 'desc')
                ->limit(20)
                ->get();

            if ($reviews->isEmpty()) {
                return response()->json([
                    'status' => 'success',
                    'data' => [],
                    'message' => 'No reviews found for this service',
                    'source' => 'empty'
                ]);
            }

            $transformedReviews = $reviews->map(function ($review) {
                return [
                    'id' => $review->id,
                    'name' => $review->name ?: 'Pengguna',
                    'avatar_url' => $review->avatar_url,
                    'rating' => (int) $review->rating,
                    'feedback' => $review->feedback,
                    'service' => $review->service,
                    'review_date' => $review->review_date
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => $transformedReviews,
                'message' => "Reviews for service $serviceId fetched successfully",
                'source' => 'database'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching reviews by service: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'success',
                'data' => [],
                'message' => 'Error fetching service reviews',
                'source' => 'error'
            ]);
        }
    }
} 