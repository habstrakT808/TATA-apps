<?php

namespace App\Http\Controllers\Mobile;
use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    
    public function getAllReviews()
{
    $reviews = DB::table('review')
        ->join('pesanan', 'review.id_pesanan', '=', 'pesanan.id_pesanan')
        ->join('users', 'pesanan.id_user', '=', 'users.id_user')
        ->join('jasa', 'pesanan.id_jasa', '=', 'jasa.id_jasa')
        ->join('paket_jasa', 'pesanan.id_paket_jasa', '=', 'paket_jasa.id_paket_jasa')
        ->select(
            'users.nama_user as name',
            'review.rating',
            DB::raw("CONCAT(jasa.kategori, ', ', paket_jasa.kelas_jasa) as service"),
            'review.review as feedback',
            'users.foto as avatar'
        )
        ->get()
        ->map(function ($item) {
            return [
                'name' => $item->name,
                'rating' => (int) $item->rating, // konversi eksplisit ke integer
                'service' => $item->service,
                'feedback' => $item->feedback,
                'avatar_url' => $item->avatar ? asset('storage/foto/' . $item->avatar) : null
            ];
        });

    return response()->json($reviews);
}
    /**
     * Create a new review for an order
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
public function addReviewByUUID(Request $request)
{
    try {
        // Log request untuk debugging
        Log::info('Review request received', [
            'uuid' => $request->uuid,
            'auth_user_id' => $request->user() ? $request->user()->id_auth : 'not authenticated',
            'headers' => $request->headers->all()
        ]);

        // Validasi input
        $validator = Validator::make($request->all(), [
            'uuid'   => 'required|exists:pesanan,uuid',
            'review' => 'required|string|min:5|max:250',
            'rating' => 'required|integer|between:1,5',
        ]);

        if ($validator->fails()) {
            Log::warning('Review validation failed', [
                'errors' => $validator->errors()->toArray()
            ]);
            
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Cek apakah user terotentikasi
        if (!$request->user()) {
            Log::error('User not authenticated for review submission');
            return response()->json([
                'status'  => 'error',
                'message' => 'User tidak terotentikasi',
            ], 401);
        }

        // Ambil data user dari auth
        $authUser = $request->user();
        Log::info('Auth user found', [
            'id_auth' => $authUser->id_auth,
            'email' => $authUser->email
        ]);
        
        // Cari user_id dari id_auth
        $user = User::where('id_auth', $authUser->id_auth)->first();
        if (!$user) {
            Log::error('User not found in users table', [
                'id_auth' => $authUser->id_auth
            ]);
            
            return response()->json([
                'status'  => 'error',
                'message' => 'User tidak ditemukan',
            ], 404);
        }
        
        Log::info('User found', [
            'id_user' => $user->id_user,
            'nama_user' => $user->nama_user
        ]);

        // Cari pesanan berdasarkan UUID dan user login
        $pesanan = Pesanan::where('uuid', $request->uuid)
            ->where('id_user', $user->id_user)->first();

        if (!$pesanan) {
            Log::warning('Order not found or does not belong to user', [
                'uuid' => $request->uuid,
                'user_id' => $user->id_user
            ]);
            
            return response()->json([
                'status'  => 'error',
                'message' => 'Pesanan tidak ditemukan atau tidak sesuai user',
            ], 404);
        }
        
        Log::info('Order found', [
            'id_pesanan' => $pesanan->id_pesanan,
            'status_pesanan' => $pesanan->status_pesanan,
            'status_pengerjaan' => $pesanan->status_pengerjaan,
            'client_confirmed_at' => $pesanan->client_confirmed_at
        ]);

        // ✅ PERBAIKI VALIDASI STATUS - Cek apakah client sudah konfirmasi selesai
        if (!$pesanan->client_confirmed_at) {
            Log::warning('Order not confirmed by client yet', [
                'id_pesanan' => $pesanan->id_pesanan,
                'status_pesanan' => $pesanan->status_pesanan,
                'status_pengerjaan' => $pesanan->status_pengerjaan,
                'client_confirmed_at' => $pesanan->client_confirmed_at
            ]);
            
            return response()->json([
                'status'  => 'error',
                'message' => 'Pesanan belum dikonfirmasi selesai oleh Anda',
            ], 403);
        }

        // Cek apakah sudah pernah direview
        $existingReview = Review::where('id_pesanan', $pesanan->id_pesanan)->first();
        if ($existingReview) {
            Log::info('Review already exists', [
                'id_review' => $existingReview->id_review
            ]);
            
            return response()->json([
                'status'  => 'error',
                'message' => 'Review untuk pesanan ini sudah ada',
                'data'    => $existingReview,
            ], 409);
        }

        // Bersihkan input review
        $cleanedReview = htmlspecialchars(strip_tags($request->review), ENT_QUOTES, 'UTF-8');

        // Simpan review baru
        $review = Review::create([
            'id_pesanan' => $pesanan->id_pesanan,
            'review'     => $cleanedReview,
            'rating'     => $request->rating,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        Log::info('Review created successfully', [
            'id_review' => $review->id_review,
            'id_pesanan' => $review->id_pesanan
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Review berhasil ditambahkan',
            'data'    => $review,
        ], 200);

    } catch (\Exception $e) {
        Log::error('Error in addReviewByUUID: ' . $e->getMessage(), [
            'exception' => get_class($e),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'status'  => 'error',
            'message' => 'Terjadi kesalahan saat menambahkan review',
            'error'   => $e->getMessage(),
        ], 500);
    }
}

    public function addReview(Request $request)
    {
        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'id_pesanan' => 'required|exists:pesanan,id_pesanan',
                'review' => 'required|string|max:250|min:5',
                'rating' => 'required|integer|between:1,5',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Sanitize input
            $sanitizedReview = strip_tags($request->review);
            $sanitizedReview = htmlspecialchars($sanitizedReview, ENT_QUOTES, 'UTF-8');

            // Check if order belongs to user and is completed
            $pesanan = Pesanan::where('id_pesanan', $request->id_pesanan)
                ->where('id_user', Auth::id())
                ->where('status', 'selesai')
                ->first();

            if (!$pesanan) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Order not found or not completed'
                ], 404);
            }

            // Check if review already exists
            $existingReview = Review::where('id_pesanan', $request->id_pesanan)->first();
            if ($existingReview) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You have already reviewed this order'
                ], 409);
            }

            // Create review
            $review = new Review();
            $review->id_pesanan = $request->id_pesanan;
            $review->review = $sanitizedReview;
            $review->rating = $request->rating;
            $review->created_at = Carbon::now();
            $review->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Thank you for your review!',
                'data' => $review
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to submit review',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all reviews by the authenticated user
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $reviews = Review::join('pesanan', 'review.id_pesanan', '=', 'pesanan.id_pesanan')
                ->where('pesanan.id_user', Auth::id())
                ->select(
                    'review.*',
                    'pesanan.uuid as order_number',
                    'pesanan.deskripsi as order_description'
                )
                ->orderBy('review.created_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $reviews
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch your reviews',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get review for a specific order
     * @param string $orderId
     * @return \Illuminate\Http\JsonResponse
     */
     

public function getReviews()
{
    try {
        // Query dengan join yang benar sesuai struktur database
        $reviews = DB::table('review')
            ->join('pesanan', 'review.id_pesanan', '=', 'pesanan.id_pesanan')
            ->join('users', 'pesanan.id_user', '=', 'users.id_user')
            ->join('jasa', 'pesanan.id_jasa', '=', 'jasa.id_jasa')
            ->select(
                'review.id_review as id',
                'users.nama_user as name',
                'users.name as full_name', // Tambahkan field name juga
                'users.foto as avatar_url',
                'review.rating',
                'review.review as feedback',
                'jasa.kategori as service',
                'pesanan.uuid as order_uuid',
                'pesanan.completed_at as completion_date',
                'review.created_at as review_date'
            )
            ->where('review.rating', '>=', 4) // Hanya review bagus
            ->whereNotNull('pesanan.completed_at') // Hanya pesanan yang sudah selesai
            ->orderBy('review.created_at', 'desc')
            ->limit(10)
            ->get();

        // Transform data untuk memastikan nama yang tepat
        $transformedReviews = $reviews->map(function ($review) {
            return [
                'id' => $review->id,
                'name' => $review->name ?: $review->full_name ?: 'Pengguna',
                'avatar_url' => $review->avatar_url,
                'rating' => (int) $review->rating,
                'feedback' => $review->feedback,
                'service' => $review->service,
                'order_uuid' => $review->order_uuid,
                'completion_date' => $review->completion_date,
                'review_date' => $review->review_date
            ];
        });

        return response()->json([
            'status' => 'success',
            'data' => $transformedReviews,
            'message' => 'Reviews fetched successfully'
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error fetching reviews: ' . $e->getMessage());
        
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to fetch reviews',
            'error' => $e->getMessage()
        ], 500);
    }
}

    // Method alternatif menggunakan Eloquent
    public function getReviewsEloquent()
    {
        try {
            $reviews = Review::with([
                'toPesanan.toUser:id_user,nama_user,name,foto',
                'toPesanan.toJasa:id_jasa,kategori'
            ])
            ->whereHas('toPesanan', function($query) {
                $query->whereNotNull('completed_at');
            })
            ->where('rating', '>=', 4)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($review) {
                $user = $review->toPesanan->toUser ?? null;
                $jasa = $review->toPesanan->toJasa ?? null;
                
                return [
                    'id' => $review->id_review,
                    'name' => $user ? ($user->nama_user ?: $user->name) : 'Pengguna',
                    'avatar_url' => $user ? $user->foto : null,
                    'rating' => (int) $review->rating,
                    'feedback' => $review->review,
                    'service' => $jasa ? $jasa->kategori : null,
                    'order_uuid' => $review->toPesanan->uuid ?? null,
                    'completion_date' => $review->toPesanan->completed_at ?? null,
                    'review_date' => $review->created_at
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => $reviews,
                'message' => 'Reviews fetched successfully'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error fetching reviews with Eloquent: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch reviews',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($orderId)
    {
        try {
            $review = Review::join('pesanan', 'review.id_pesanan', '=', 'pesanan.id_pesanan')
                ->where('pesanan.uuid', $orderId)
                ->where('pesanan.id_user', Auth::id())
                ->select(
                    'review.*',
                    'pesanan.uuid as order_number',
                    'pesanan.deskripsi as order_description'
                )
                ->first();

            if (!$review) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Review not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $review
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch review',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 