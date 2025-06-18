<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mobile\UserController;
use App\Http\Controllers\Mobile\JasaController;
use App\Http\Controllers\Mobile\PesananController;
use App\Http\Controllers\Mobile\TransaksiController;
use App\Http\Controllers\Mobile\MailController;
use App\Http\Controllers\Mobile\ReviewController;

use App\Http\Controllers\Mobile\PengerjaanController;
use App\Http\Controllers\Mobile\MetodePembayaranController;
use App\Http\Controllers\Services\ChatController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request){
    return $request->user();
});

Route::group(['prefix'=>'/mobile'], function(){
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

        });
        
        //API only pengerjaan route
        Route::group(['prefix'=>'/pengerjaan'], function(){
            Route::get('/', [PengerjaanController::class, 'getAll']);
            Route::get('/{id_revisi}', [PengerjaanController::class, 'getDetail']);
            Route::post('/{id_revisi}/request-revisi', [PengerjaanController::class, 'requestRevision']);
            Route::get('/{id_revisi}/download', [PengerjaanController::class, 'downloadFiles']);
            Route::get('/{id_revisi}/history', [PengerjaanController::class, 'getRevisionHistory']);
            Route::get('/{id_revisi}/{revisionUuid}', [PengerjaanController::class, 'getDetail']);
            Route::post('/{id_revisi}/accept-work', [PengerjaanController::class, 'acceptWork']);
            Route::post('/download', [PengerjaanController::class, 'downloadFiles']);
        });

        //API only metode pembayaran route
        Route::group(['prefix'=>'/metode-pembayaran'], function(){
            Route::get('/', [MetodePembayaranController::class, 'showAll']);
            Route::get('/{uuid}', [MetodePembayaranController::class, 'showDetail']);
        });

        //API only transaksi route
        Route::group(['prefix'=>'/transaksi'], function(){
            Route::get('/', [TransaksiController::class, 'getAll']);
            Route::get('/{order_id}', [TransaksiController::class, 'getDetail']);
            Route::post('/create', [TransaksiController::class, 'createTransaction']);
            Route::post('/upload-payment', [TransaksiController::class, 'uploadPaymentProof']);
            Route::get('/details/{orderId}', [TransaksiController::class, 'getTransactionDetails']);
            Route::get('/user-transactions', [TransaksiController::class, 'getUserTransactions']);
            Route::post('/cancel', [TransaksiController::class, 'cancelTransaction']);
        });

        Route::group(['prefix'=>'/users'],function(){
            Route::group(['prefix'=>'/profile'],function(){
                Route::post('/', [UserController::class, 'getProfile']);
                Route::post('/update', [UserController::class, 'updateProfile']);
                Route::post('/foto', [UserController::class, 'checkFotoProfile']);
            });
            // Auth routes for logout
            Route::post('/logout', [UserController::class, 'logout']);
            Route::post('/logout-all', [UserController::class, 'logoutAll']);
        });
        Route::get('/dashboard',[UserController::class, 'dashboard']);
    });

    Route::group(['middleware' => 'user.guest'], function(){
        Route::group(['prefix'=>'/users'],function(){
            Route::post('/logingoogle', [UserController::class,'logingoogle']);
            //review 
            Route::get('/review', [ReviewController::class, 'getAllReviews']);

            Route::post('/CekEmail', [UserController::class,'CekEmail']);
            Route::post('/login', [UserController::class,'login']);
            Route::post('/register', [UserController::class,'register']);
        });
        // Tambahkan akses jasa untuk guest
        Route::group(['prefix'=>'/jasa'], function(){
            Route::get('/', [JasaController::class, 'showAll']);
            Route::get('/{id?}', [JasaController::class, 'show']);
            Route::get('/detail/{any}', [JasaController::class, 'showDetail']);
        });
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
    Route::post('/send', [ChatController::class, 'sendMessage']);
    Route::get('/messages', [ChatController::class, 'getMessages']);
    Route::post('/mark-read', [ChatController::class, 'markAsRead']);
    Route::post('/update-fcm-token', [ChatController::class, 'updateFcmToken']);
});

// Test Firebase tanpa auth
Route::get('/test-firebase', function() {
    try {
        $credentials = config('firebase.credentials.file');
        $projectId = config('firebase.project_id');

        return response()->json([
            'success' => true,
            'message' => 'Firebase configuration loaded successfully',
            'data' => [
                'project_id' => $projectId,
                'credentials_exists' => file_exists($credentials)
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], 500);
    }
});

// Test Firebase connection (sederhana)
Route::post('/test-save', function(Request $request) {
    try {
        $request->validate([
            'pesanan_uuid' => 'required|string',
            'sender_uuid' => 'required|string',
            'message' => 'required|string',
        ]);

        // Cek file kredensial ada
        $credentials = config('firebase.credentials.file');
        $projectId = config('firebase.project_id');
        
        // Buat folder struktur bila perlu
        $path = storage_path('app/firebase/chat');
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        
        // Simpan pesan sebagai file JSON
        $messageData = [
            'pesanan_uuid' => $request->pesanan_uuid,
            'sender_uuid' => $request->sender_uuid,
            'message' => $request->message,
            'timestamp' => time(),
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $filename = $path . '/' . time() . '_' . uniqid() . '.json';
        file_put_contents($filename, json_encode($messageData, JSON_PRETTY_PRINT));
        
        return response()->json([
            'success' => true,
            'message' => 'Pesan disimpan secara lokal (Firestore simulation)',
            'message_id' => basename($filename, '.json'),
            'data' => [
                'firebase_config' => [
                    'project_id' => $projectId,
                    'credentials_exists' => file_exists($credentials)
                ],
                'message' => $messageData
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Guest routes
Route::group(['middleware' => ['api']], function () {
    // ... existing routes ...

    // Review route
    Route::get('/users/review', [App\Http\Controllers\Services\ReviewController::class, 'getAllReviews']);
    
    // Jasa route
    Route::get('/jasa/{id}', [App\Http\Controllers\Services\JasaController::class, 'getJasaById']);
    Route::get('/mobile/jasa/{id}', [App\Http\Controllers\Services\JasaController::class, 'getJasaById']);
    
    // Pengerjaan route - perbaiki dengan namespace yang benar
    Route::get('/mobile/pengerjaan/{id}', [\App\Http\Controllers\Api\Mobile\PengerjaanController::class, 'getDetail']);
    
    // ... rest of the code ...
});

// Test route untuk debugging jasa
Route::get('/debug-jasa', function() {
    $jasaList = \App\Models\Jasa::all();
    $jasaData = [];
    
    foreach($jasaList as $jasa) {
        $paketJasa = \App\Models\PaketJasa::where('id_jasa', $jasa->id_jasa)->get();
        $jasaData[] = [
            'jasa' => $jasa,
            'paket_jasa' => $paketJasa
        ];
    }
    
    return response()->json([
        'status' => 'success',
        'data' => $jasaData
    ]);
});