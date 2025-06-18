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
        // Validasi input
        $validator = Validator::make($request->all(), [
            'uuid'   => 'required|exists:pesanan,uuid',
            'review' => 'required|string|min:5|max:250',
            'rating' => 'required|integer|between:1,5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validasi gagal',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Cari pesanan berdasarkan UUID dan user login
        $pesanan = Pesanan::where('uuid', $request->uuid)
            ->where('id_user', User::select('id_user')->where('id_auth', $request->user()->id_auth)->first()->id_user)->first();

        if (!$pesanan) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Pesanan tidak ditemukan atau tidak sesuai user',
            ], 404);
        }

        // Optional: Cek apakah pesanan sudah selesai (jika perlu)
        if ($pesanan->status_pesanan !== 'selesai') {
            return response()->json([
                'status'  => 'error',
                'message' => 'Hanya pesanan yang sudah selesai dapat direview',
            ], 403);
        }

        // Cek apakah sudah pernah direview
        $existingReview = Review::where('id_pesanan', $pesanan->id_pesanan)->first();
        if ($existingReview) {
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

        return response()->json([
            'status'  => 'success',
            'message' => 'Review berhasil ditambahkan',
            'data'    => $review,
        ], 200);

    } catch (\Exception $e) {
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
    $reviews = DB::table('review')
        ->join('pesanan', 'review.id_pesanan', '=', 'pesanan.id_pesanan')
        ->join('users', 'pesanan.id_user', '=', 'users.id_user')
        ->join('jasa', 'pesanan.id_jasa', '=', 'jasa.id_jasa')
        ->join('paket_jasa', 'pesanan.id_paket_jasa', '=', 'paket_jasa.id_paket_jasa')
        ->select(
            'users.nama_user as name',
            DB::raw('CAST(review.rating AS UNSIGNED) as rating'),
            DB::raw("CONCAT(jasa.kategori, ', ', paket_jasa.nama_paket_jasa) as service"),
            'review.review as feedback',
            'users.foto as avatar'
        )
        ->get()
        ->map(function ($item) {
            return [
                'name' => $item->name,
                'rating' =>(int) $item->rating,
                'feedback' => $item->feedback,
                'avatar_url' => $item->avatar ? asset('storage/foto/' . $item->avatar) : null
            ];
        });

    return response()->json($reviews);
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