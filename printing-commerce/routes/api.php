<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mobile\UserController;
use App\Http\Controllers\Mobile\JasaController;
use App\Http\Controllers\Mobile\PesananController;
use App\Http\Controllers\Mobile\TransaksiController;
use App\Http\Controllers\Mobile\MailController;
use App\Http\Controllers\Mobile\ReviewController;
use App\Http\Controllers\Mobile\ChatController;
use App\Http\Controllers\Mobile\PengerjaanController;
use App\Http\Controllers\Mobile\MetodePembayaranController;
use App\Http\Controllers\Services\ChatController as ServicesChatController;
use App\Http\Controllers\Api\ChatApiController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request){
    return $request->user();
});

Route::group(['prefix'=>'/mobile'], function(){
    // Routes that don't require authentication
    Route::post('/users/login', [UserController::class, 'login']);
    Route::post('/users/register', [UserController::class, 'register']);
    Route::post('/users/forgot-password', [UserController::class, 'forgotPassword']);
    Route::post('/users/check-email', [UserController::class, 'checkEmail']);
    Route::post('/users/login-google', [UserController::class, 'loginGoogle']);
    Route::post('/users/refresh-token', [UserController::class, 'refreshToken']);
    
    Route::group(['middleware'=>'auth.mobile'], function(){
        
        //API only jasa route
        Route::group(['prefix'=>'/jasa'], function(){
            Route::get('/',[JasaController::class,'showAll']);
            Route::get('/{id?}', [JasaController::class, 'show']);

            Route::get('/detail/{any}',[JasaController::class,'showDetail']);
        });

        //API only pesanan route
        Route::group(['prefix'=>'/pesanan'], function(){
            Route::get('/', [PesananController::class, 'getAll']);
            Route::post('/create-with-transaction', [PesananController::class, 'createPesananWithTransaction']);
            Route::post('/create', [PesananController::class, 'create']);
            Route::post('/cancel', [PesananController::class, 'cancel']);
            Route::post('/download', [PesananController::class, 'downloadFiles']);
            Route::post('/review/add-by-uuid', [ReviewController::class, 'addReviewByUUID']);
            Route::get('/detail/{uuid}', [PesananController::class, 'getDetail']);
        });
        
        //API only pengerjaan route
        Route::group(['prefix'=>'/pengerjaan'], function(){
            Route::get('/', [PengerjaanController::class, 'getAll']);
            Route::get('/{uuid}', [PengerjaanController::class, 'getDetail']);
            Route::post('/upload-hasil', [PengerjaanController::class, 'uploadHasil']);
            Route::post('/revisi', [PengerjaanController::class, 'requestRevisi']);
        });

        //API only transaksi route
        Route::group(['prefix'=>'/transaksi'], function(){
            Route::post('/upload-bukti', [TransaksiController::class, 'uploadBukti']);
            Route::post('/cancel', [TransaksiController::class, 'cancelTransaction']);
            Route::get('/detail/{order_id}', [TransaksiController::class, 'getDetail']);
        });
        
        //API only metode pembayaran route
        Route::group(['prefix'=>'/metode-pembayaran'], function(){
            Route::get('/', [MetodePembayaranController::class, 'getAll']);
            Route::get('/{uuid}', [MetodePembayaranController::class, 'getDetail']);
        });
        
        //API only user route
        Route::group(['prefix'=>'/user'], function(){
            Route::get('/profile', [UserController::class, 'getProfile']);
            Route::post('/profile/update', [UserController::class, 'updateProfile']);
            Route::post('/profile/update-photo', [UserController::class, 'updatePhoto']);
            Route::post('/change-password', [UserController::class, 'changePassword']);
            Route::post('/logout', [UserController::class, 'logout']);
            Route::post('/logout-all', [UserController::class, 'logoutAll']);
            Route::delete('/delete', [UserController::class, 'deleteUser']);
        });
        
        //API only mail route
        Route::group(['prefix'=>'/mail'], function(){
            Route::post('/send-otp', [MailController::class, 'sendOTP']);
            Route::post('/verify-otp', [MailController::class, 'verifyOTP']);
            Route::post('/send-email-verification', [MailController::class, 'sendEmailVerification']);
            Route::post('/verify-email', [MailController::class, 'verifyEmail']);
        });
        
        //API only review route
        Route::group(['prefix'=>'/review'], function(){
            Route::get('/', [ReviewController::class, 'getAll']);
            Route::get('/{id}', [ReviewController::class, 'getDetail']);
            Route::post('/add', [ReviewController::class, 'addReview']);
        });
        
        //API only verify route
        Route::group(['prefix'=>'/verify'],function(){
            Route::group(['prefix'=>'/create'],function(){
                Route::post('/password',[MailController::class, 'createForgotPassword']);
                Route::post('/email',[MailController::class, 'createVerifyEmail']);
            });
            Route::group(['prefix'=>'/password'],function(){
                Route::get('/{any?}',[UserController::class, 'getChangePass'])->where('any','.*');
                Route::post('/',[UserController::class, 'changePassEmail']);
            });
            Route::group(['prefix'=>'/email'],function(){
                Route::get('/{any?}',[UserController::class, 'verifyEmail'])->where('any','.*');
                Route::post('/',[UserController::class, 'verifyEmail'])->where('any','.*');
            });
            Route::group(['prefix'=>'/otp'],function(){
                Route::post('/password',[UserController::class, 'getChangePass']);
                Route::post('/email',[UserController::class, 'verifyEmail']);
            });
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

// Test Firebase tanpa auth
Route::get('/test-firebase', function () {
    $messaging = app('firebase.messaging');
    $message = $messaging->newMessage();
    $message->withNotification([
        'title' => 'Test Notification',
        'body' => 'This is a test notification from Laravel'
    ]);
    $message->withData([
        'key1' => 'value1',
        'key2' => 'value2'
    ]);
    
    try {
        $messaging->send($message, ['YOUR_FCM_TOKEN_HERE']);
        return response()->json(['status' => 'success', 'message' => 'Notification sent']);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
    }
});

// API Login
Route::post('/login', [UserController::class, 'login']);
Route::post('/register', [UserController::class, 'register']);
Route::post('/forgot-password', [UserController::class, 'forgotPassword']);
Route::post('/reset-password', [UserController::class, 'resetPassword']);

// API Jasa
Route::get('/jasa', [JasaController::class, 'showAll']);
Route::get('/jasa/{id}', [JasaController::class, 'show']);

// API Metode Pembayaran
Route::get('/metode-pembayaran', [MetodePembayaranController::class, 'getAll']);
Route::get('/metode-pembayaran/{uuid}', [MetodePembayaranController::class, 'getDetail']);

// API untuk Firebase Cloud Messaging
Route::post('/register-fcm-token', [UserController::class, 'registerFcmToken'])->middleware('auth:sanctum');
Route::post('/send-notification', [ServicesChatController::class, 'sendNotification'])->middleware('auth:sanctum');

// API untuk upload file
Route::post('/upload-file', [UserController::class, 'uploadFile'])->middleware('auth:sanctum');

// API untuk download file
Route::get('/download-file/{filename}', [UserController::class, 'downloadFile'])->middleware('auth:sanctum');

// API untuk generate link
Route::get('/generate-link/{filename}', [UserController::class, 'generateLink'])->middleware('auth:sanctum');

// API untuk cek status
Route::get('/check-status', [UserController::class, 'checkStatus'])->middleware('auth:sanctum');

// API untuk cek versi
Route::get('/check-version', [UserController::class, 'checkVersion']);

// API untuk cek maintenance
Route::get('/check-maintenance', [UserController::class, 'checkMaintenance']);

// API untuk cek server
Route::get('/check-server', [UserController::class, 'checkServer']);

// API untuk cek koneksi
Route::get('/check-connection', [UserController::class, 'checkConnection']);

// API untuk cek auth
Route::get('/check-auth', [UserController::class, 'checkAuth'])->middleware('auth:sanctum');

// API untuk logout
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');

// API untuk logout all
Route::post('/logout-all', [UserController::class, 'logoutAll'])->middleware('auth:sanctum');

// API untuk delete account
Route::delete('/delete-account', [UserController::class, 'deleteAccount'])->middleware('auth:sanctum');

// API untuk update profile
Route::post('/update-profile', [UserController::class, 'updateProfile'])->middleware('auth:sanctum');

// API untuk update photo
Route::post('/update-photo', [UserController::class, 'updatePhoto'])->middleware('auth:sanctum');

// API untuk change password
Route::post('/change-password', [UserController::class, 'changePassword'])->middleware('auth:sanctum');

// API untuk get profile
Route::get('/profile', [UserController::class, 'getProfile'])->middleware('auth:sanctum');

// API untuk get user
Route::get('/user', [UserController::class, 'getUser'])->middleware('auth:sanctum');

// API untuk get users
Route::get('/users', [UserController::class, 'getUsers'])->middleware('auth:sanctum');

// API untuk get user by id
Route::get('/user/{id}', [UserController::class, 'getUserById'])->middleware('auth:sanctum');

// API untuk get user by email
Route::get('/user/email/{email}', [UserController::class, 'getUserByEmail'])->middleware('auth:sanctum');

// API untuk get user by username
Route::get('/user/username/{username}', [UserController::class, 'getUserByUsername'])->middleware('auth:sanctum');

// API untuk get user by phone
Route::get('/user/phone/{phone}', [UserController::class, 'getUserByPhone'])->middleware('auth:sanctum');

// Tambahan rute API untuk chat mobile
Route::group(['prefix' => 'chat', 'middleware' => ['auth:sanctum']], function() {
    Route::post('/create', [ChatController::class, 'createOrGetChatRoom']);
    Route::get('/list', [ChatController::class, 'getChatList']);
    Route::get('/messages', [ChatController::class, 'getMessages']);
    Route::post('/send', [ChatController::class, 'sendMessage']);
    Route::post('/upload', [ChatController::class, 'uploadFile']);
});

// Tambahan rute API untuk chat mobile yang kompatibel dengan frontend Flutter
Route::group(['prefix' => 'mobile/chat', 'middleware' => ['auth.mobile']], function() {
    Route::post('/create', [ChatController::class, 'createOrGetChatRoom']);
    Route::post('/get-or-create', [ChatController::class, 'getOrCreateChatForOrder']);
    Route::get('/list', [ChatController::class, 'getChatList']);
    Route::get('/messages', [ChatController::class, 'getMessages']);
    Route::post('/send', [ChatController::class, 'sendMessage']);
    Route::post('/send-by-pesanan', [ChatController::class, 'sendMessageByPesanan']);
    Route::post('/upload', [ChatController::class, 'uploadFile']);
    Route::post('/mark-read', [ChatController::class, 'markMessagesAsRead']);
    Route::get('/messages-by-pesanan/{pesananUuid}', [ChatController::class, 'getMessagesByPesanan']);
});

// API endpoint for fetching reviews (accessible without authentication)
Route::get('/users/review', [ReviewController::class, 'getAllReviews']);