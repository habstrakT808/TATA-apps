<?php
// routes/api.php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mobile\UserController;
use App\Http\Controllers\Mobile\JasaController;
use App\Http\Controllers\Mobile\PesananController;
use App\Http\Controllers\Mobile\MetodePembayaranController;
use App\Http\Controllers\Mobile\TransaksiController;
use App\Http\Controllers\Mobile\MailController;
use App\Http\Controllers\Mobile\ReviewController;
use App\Http\Controllers\Mobile\ChatController;
use App\Http\Controllers\Mobile\PengerjaanController;
use App\Http\Controllers\Services\ChatController as ServicesChatController;
use App\Http\Controllers\Api\ChatApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes (tidak perlu authentication)
Route::prefix('mobile')->group(function () {
    // Authentication routes
    Route::post('users/login', [UserController::class, 'login']);
    Route::post('users/register', [UserController::class, 'register']);
    Route::post('users/login-google', [UserController::class, 'logingoogle']);
    Route::post('users/check-email', [UserController::class, 'checkEmail']);
    Route::post('users/forgot-password', [UserController::class, 'forgotPassword']);
    Route::post('users/change-password', [UserController::class, 'changePassEmail']);
    Route::post('users/refresh-token', [UserController::class, 'refreshToken']);
    
    // Public jasa routes (PINDAHKAN KE SINI AGAR TIDAK PERLU AUTH)
    Route::get('jasa', [JasaController::class, 'showAll']);
    Route::get('jasa/{id}', [JasaController::class, 'show']); // PINDAHKAN KE PUBLIC
});

// Protected routes (perlu authentication)
Route::middleware('auth:sanctum')->group(function () { // HAPUS debug.auth middleware
    Route::prefix('mobile')->group(function () {
        // Debug route
        Route::get('debug/auth', function (Request $request) {
            return response()->json([
                'status' => 'success',
                'message' => 'Authentication successful',
                'user' => [
                    'id' => $request->user()->id_auth,
                    'email' => $request->user()->email,
                    'role' => $request->user()->role,
                ]
            ]);
        });
        
        // User routes
        Route::get('user/profile', [UserController::class, 'getProfile']);
        Route::post('user/profile/update', [UserController::class, 'updateProfile']);
        Route::post('user/profile/update-photo', [UserController::class, 'updatePhoto']);
        Route::post('users/logout', [UserController::class, 'logout']);
        Route::post('users/logout-all', [UserController::class, 'logoutAll']);
        Route::post('users/update-fcm-token', [UserController::class, 'updateFCMToken']);
        
        // Review routes
        Route::get('users/review', [UserController::class, 'getUserReviews']);
        
        // Jasa routes (yang perlu auth)
        Route::get('jasa/detail/{any}', [JasaController::class, 'showDetail']);
        
        // Pesanan routes
        Route::prefix('pesanan')->group(function() {
            Route::get('/', [PesananController::class, 'getAll']);
            Route::post('/create-with-transaction', [PesananController::class, 'createPesananWithTransaction']);
            Route::post('/create', [PesananController::class, 'create']);
            Route::post('/cancel', [PesananController::class, 'cancel']);
            Route::post('/download', [PesananController::class, 'downloadFiles']);
            Route::post('/review/add-by-uuid', [ReviewController::class, 'addReviewByUUID']);
            Route::get('/detail/{uuid}', [PesananController::class, 'getDetail']);
            
            // ✅ TAMBAH ROUTE INI UNTUK ORDER INFO
            Route::get('/order-info/{uuid}', [PesananController::class, 'getOrderInfo']);
        });
        
        // Pengerjaan routes
        Route::prefix('pengerjaan')->group(function() {
            Route::get('/', [PengerjaanController::class, 'getAll']);
            Route::get('/{uuid}', [PengerjaanController::class, 'getDetail']);
            Route::post('/upload-hasil', [PengerjaanController::class, 'uploadHasil']);
            Route::post('/revisi', [PengerjaanController::class, 'requestRevisi']);
        });
        
        // Transaksi routes
        Route::prefix('transaksi')->group(function() {
            Route::post('/upload-bukti', [TransaksiController::class, 'uploadBukti']);
            Route::post('/cancel', [TransaksiController::class, 'cancelTransaction']);
            Route::get('/detail/{order_id}', [TransaksiController::class, 'getDetail']);
        });
        
        // Metode pembayaran routes
        Route::prefix('metode-pembayaran')->group(function() {
            Route::get('/', [MetodePembayaranController::class, 'getAll']);
            Route::get('/{uuid}', [MetodePembayaranController::class, 'getDetail']);
        });
        
        // Mail routes
        Route::prefix('mail')->group(function() {
            Route::post('/send-otp', [MailController::class, 'sendOTP']);
            Route::post('/verify-otp', [MailController::class, 'verifyOTP']);
            Route::post('/send-email-verification', [MailController::class, 'sendEmailVerification']);
            Route::post('/verify-email', [MailController::class, 'verifyEmail']);
        });
        
        // Chat routes
        Route::prefix('chat')->group(function() {
            Route::post('/create', [ChatController::class, 'createOrGetChatRoom']);
            Route::get('/list', [ChatController::class, 'getChatList']);
            Route::get('/messages', [ChatController::class, 'getMessages']);
            
            // ✅ UBAH ROUTE INI - tambahkan prefix untuk membedakan
            Route::get('/messages/order/{orderId}', [ChatController::class, 'getMessagesByOrderId']);
            
            Route::get('/messages/{pesanan_uuid}', [ChatController::class, 'getMessagesByPesanan']);
            Route::post('/send', [ChatController::class, 'sendMessage']);
            Route::post('/send-by-pesanan', [ChatController::class, 'sendMessageByPesanan']);
            Route::post('/upload', [ChatController::class, 'uploadFile']);
            Route::post('/create-for-order', [ChatController::class, 'getOrCreateChatForOrder']);
            Route::post('/mark-read', [ChatController::class, 'markMessagesAsRead']);
            Route::post('/send-notification', [ChatController::class, 'sendNotification']);
            Route::post('/sync-message', [ChatController::class, 'syncMessage']);
            
            // ✅ DEBUG ROUTE
            Route::get('/debug-list', [ChatController::class, 'debugChatList']);
        });
    });
});

// Chat API Routes
Route::middleware('auth:sanctum')->prefix('chat')->group(function () {
    Route::post('/send', [ServicesChatController::class, 'sendMessage']);
    Route::get('/messages', [ServicesChatController::class, 'getMessages']);
    Route::post('/mark-read', [ServicesChatController::class, 'markAsRead']);
    Route::post('/update-fcm-token', [ServicesChatController::class, 'updateFcmToken']);
});

// New Chat API Routes (untuk versi UI Figma)
Route::prefix('chats')->group(function () {
    Route::get('/', [ChatApiController::class, 'getChats']);
    Route::get('/{chatUuid}', [ChatApiController::class, 'getChatDetail']);
    Route::post('/send', [ChatApiController::class, 'sendMessage']);
    Route::post('/create', [ChatApiController::class, 'createChat']);
    Route::post('/notify', [ChatApiController::class, 'sendNotification']);
});

// API untuk Firebase Cloud Messaging
Route::post('/register-fcm-token', [UserController::class, 'registerFcmToken'])->middleware('auth:sanctum');
Route::post('/send-notification', [ServicesChatController::class, 'sendNotification'])->middleware('auth:sanctum');

// Fallback route untuk debugging
Route::fallback(function () {
    return response()->json([
        'status' => 'error',
        'message' => 'API route not found. Please check your endpoint.',
        'requested_url' => request()->fullUrl(),
        'method' => request()->method(),
    ], 404);
});