<?php
namespace App\Http\Controllers\Services;
use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReviewController extends Controller
{
    /**
     * Create a new review
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'id_pesanan' => 'required|exists:pesanan,id_pesanan',
                'review' => 'required|string|max:250|min:5',
                'rating' => 'required|integer|between:1,5',
            ]);

            if ($validator->fails()) {
                $errors = [];
                foreach ($validator->errors()->toArray() as $field => $errorMessages){
                    $errors[$field] = $errorMessages[0];
                    break;
                }
                return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
            }

            // Sanitize input
            $sanitizedReview = strip_tags($request->review);
            $sanitizedReview = htmlspecialchars($sanitizedReview, ENT_QUOTES, 'UTF-8');

            // Check if order belongs to user
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
                    'message' => 'Review already exists for this order'
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
                'message' => 'Review created successfully',
                'data' => $review
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while creating the review',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get reviews for authenticated user
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserReviews()
    {
        try {
            $reviews = Review::join('pesanan', 'review.id_pesanan', '=', 'pesanan.id_pesanan')
                ->where('pesanan.id_user', Auth::id())
                ->select('review.*', 'pesanan.uuid as order_number')
                ->orderBy('review.created_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $reviews
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching reviews',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all reviews with pagination and filters
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'per_page' => 'nullable|integer|min:1|max:100',
                'rating' => 'nullable|integer|between:1,5',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'search' => 'nullable|string|max:100'
            ]);

            if ($validator->fails()) {
                $errors = [];
                foreach ($validator->errors()->toArray() as $field => $errorMessages){
                    $errors[$field] = $errorMessages[0];
                    break;
                }
                return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
            }

            $perPage = $request->input('per_page', 10);
            $query = Review::join('pesanan', 'review.id_pesanan', '=', 'pesanan.id_pesanan')
                ->join('users', 'pesanan.id_user', '=', 'users.id_user')
                ->select(
                    'review.*',
                    'pesanan.uuid as order_number',
                    'pesanan.deskripsi as order_description',
                    'users.nama_user',
                    'pesanan.created_at as order_date'
                );

            // Apply filters
            if ($request->has('rating')) {
                $query->where('review.rating', $request->rating);
            }

            if ($request->has('start_date')) {
                $query->whereDate('review.created_at', '>=', $request->start_date);
            }

            if ($request->has('end_date')) {
                $query->whereDate('review.created_at', '<=', $request->end_date);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('review.review', 'LIKE', "%{$search}%")
                      ->orWhere('users.nama_user', 'LIKE', "%{$search}%")
                      ->orWhere('pesanan.uuid', 'LIKE', "%{$search}%");
                });
            }

            $reviews = $query->orderBy('review.created_at', 'desc')
                           ->paginate($perPage);

            return response()->json([
                'status' => 'success',
                'data' => $reviews
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch reviews',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get review statistics and analytics
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatistics(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            if ($validator->fails()) {
                $errors = [];
                foreach ($validator->errors()->toArray() as $field => $errorMessages){
                    $errors[$field] = $errorMessages[0];
                    break;
                }
                return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
            }

            $query = Review::query();

            if ($request->has('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->has('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            $stats = [
                'total_reviews' => $query->count(),
                'average_rating' => round($query->avg('rating'), 2),
                'rating_distribution' => $query->selectRaw('rating, COUNT(*) as count')
                    ->groupBy('rating')
                    ->orderBy('rating')
                    ->get(),
                'recent_reviews' => Review::join('pesanan', 'review.id_pesanan', '=', 'pesanan.id_pesanan')
                    ->join('users', 'pesanan.id_user', '=', 'users.id_user')
                    ->select(
                        'review.*',
                        'pesanan.uuid as order_number',
                        'users.nama_user'
                    )
                    ->orderBy('review.created_at', 'desc')
                    ->limit(5)
                    ->get(),
                'monthly_average' => Review::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, AVG(rating) as average_rating, COUNT(*) as count')
                    ->groupBy('month')
                    ->orderBy('month', 'desc')
                    ->limit(12)
                    ->get()
            ];

            return response()->json([
                'status' => 'success',
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch review statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete inappropriate review
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            $review = Review::find($id);
            
            if (!$review) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Review not found'
                ], 404);
            }

            $review->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Review deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete review',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get review details by ID
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $review = Review::join('pesanan', 'review.id_pesanan', '=', 'pesanan.id_pesanan')
                ->join('users', 'pesanan.id_user', '=', 'users.id_user')
                ->where('review.id_review', $id)
                ->select(
                    'review.*',
                    'pesanan.uuid as order_number',
                    'pesanan.deskripsi as order_description',
                    'users.nama_user',
                    'users.email',
                    'pesanan.created_at as order_date'
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
                'message' => 'Failed to fetch review details',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all reviews for public view
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllReviews()
    {
        try {
            $reviews = Review::join('pesanan', 'review.id_pesanan', '=', 'pesanan.id_pesanan')
                ->join('users', 'pesanan.id_user', '=', 'users.id_user')
                ->select(
                    'review.id_review as id',
                    'users.nama_user as name',
                    'review.rating',
                    'review.review as feedback',
                    'users.foto as avatar_url'
                )
                ->orderBy('review.created_at', 'desc')
                ->limit(5)
                ->get();

            return $reviews;

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch reviews',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}