<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\Http\Controllers\Services\JasaController;
use App\Http\Controllers\Services\PesananController;
use App\Http\Controllers\Services\MetodePembayaranController;
use App\Http\Controllers\Services\TransaksiController;
use App\Http\Controllers\Services\AdminController;
use App\Http\Controllers\Services\EditorController;
use App\Http\Controllers\Services\PengerjaanController;
// Comment out missing controllers
// use App\Http\Controllers\Mobile\ChatController;
// use App\Http\Controllers\Mobile\UserController;

use App\Http\Controllers\Page\JasaController AS ShowJasaController;
use App\Http\Controllers\Page\PesananController AS ShowPesananController;
use App\Http\Controllers\Page\MetodePembayaranController AS ShowMetodePembayaranController;
use App\Http\Controllers\Page\TransaksiController AS ShowTransaksiController;
use App\Http\Controllers\Page\ChatController AS ShowChatController;
use App\Http\Controllers\Page\AdminController AS ShowAdminController;
use App\Http\Controllers\Page\EditorController AS ShowEditorController;
use App\Http\Controllers\Page\UserController AS ShowUserController;
use App\Http\Controllers\Page\PengerjaanController AS ShowPengerjaanController;
use App\Http\Controllers\Page\UserManagementController;
use App\Http\Controllers\Page\PesananDetailController;
Route::group(['middleware'=>['auth:sanctum','authorize']], function(){
    //API only jasa route
    Route::group(['prefix'=>'/jasa'], function(){
        //page jasa
        Route::get('/',[ShowJasaController::class,'showAll'])->name('jasa.index');
        Route::get('/tambah',[ShowJasaController::class,'showTambah'])->name('jasa.tambah');
        Route::get('/edit/{any}',[ShowJasaController::class,'showEdit'])->name('jasa.edit');
        Route::get('/edit', function(){
            return redirect('/jasa');
        });
        // route for jasa
        Route::post('/create',[JasaController::class,'createJasa'])->name('api.jasa.create');
        Route::put('/update',[JasaController::class,'updateJasa'])->name('api.jasa.update');
        Route::delete('/delete',[JasaController::class,'deleteJasa'])->name('api.jasa.delete');
    });

    //API only pesanan route
    Route::group(['prefix'=>'/pesanan'], function(){
        //page pesanan
        Route::get('/',[ShowPesananController::class,'showAll']);
        Route::get('/detail/{uuid}',[ShowPesananController::class,'showDetail']);
        Route::get('/statistics', [PesananController::class, 'getStatistics']);
        // route for pesanan
        Route::put('/update', [App\Http\Controllers\Page\PesananDetailController::class, 'updatePesanan']);
        Route::post('/upload-hasil-desain', [App\Http\Controllers\Page\PesananDetailController::class, 'uploadHasilDesain']);
        Route::get('/editors', [App\Http\Controllers\Page\PesananDetailController::class, 'getEditors']);
        Route::delete('/delete', [PesananController::class, 'deletePesanan']);
    });
    
    //API only metode pembayaran route
    Route::group(['prefix'=>'/payment-methods'], function(){
        //page metode pembayaran
        Route::get('/',[ShowMetodePembayaranController::class,'showAll']);
        Route::get('/detail/{uuid}',[ShowMetodePembayaranController::class,'showDetail']);
        Route::get('/tambah',[ShowMetodePembayaranController::class,'showTambah']);
        Route::get('/edit/{any}',[ShowMetodePembayaranController::class,'showEdit']);
        Route::get('/edit', function(){
            return redirect('/payment-methods');
        });
        // route for metode pembayaran
        Route::post('/create',[MetodePembayaranController::class,'createMPembayaran']);
        Route::put('/update',[MetodePembayaranController::class,'updateMPembayaran']);
        Route::delete('/delete',[MetodePembayaranController::class,'deleteMPembayaran']);
    });

    // //API only transaksi route
    // Route::group(['prefix'=>'/transaksi'], function(){
    //     //page transaksi
    //     Route::get('/',[ShowTransaksiController::class,'showAll']);
    //     Route::get('/detail/{any}',[ShowTransaksiController::class,'showDetail']);
    //     Route::get('/pending', [ShowTransaksiController::class, 'getPendingPayments']);
    //     //route for transaksi
    //     Route::post('/confirm', [TransaksiController::class, 'confirmPayment']);
    //     Route::post('/reject', [TransaksiController::class, 'rejectPayment']);
    // });

    //API only chat route
    Route::group(['prefix'=>'/chat'], function(){
        // PERBAIKAN: Pindahkan API routes ke ATAS sebelum route dengan parameter
        Route::get('/chats', [ShowChatController::class, 'getChats']);
        Route::get('/messages', [ShowChatController::class, 'getMessages']);
        Route::post('/send', [ShowChatController::class, 'sendMessage']);
        Route::post('/mark-read', [ShowChatController::class, 'markAsRead']);
        Route::post('/upload', [ShowChatController::class, 'uploadFile']);
        Route::post('/assign', [ShowChatController::class, 'assignChatToAdmin']);
        
        // Page routes di bawah
        Route::get('/',[ShowChatController::class,'showAll']);
        Route::get('/{uuid}', [ShowChatController::class, 'showDetail']);
    });

    //API only user management route
    Route::group(['prefix'=>'/user-management'], function(){
        //page user management
        Route::get('/',[UserManagementController::class,'showAll']);
        Route::get('/detail/{uuid}',[UserManagementController::class,'showDetail']);
        Route::get('/tambah',[UserManagementController::class,'showTambah']);
        Route::get('/edit', function(){
            return redirect('/user-management');
        });
    });

    //API only user route - keep for backward compatibility
    Route::group(['prefix'=>'/user'], function(){
        Route::get('/', function(){
            return redirect('/user-management');
        });
        Route::post('/create', [App\Http\Controllers\Mobile\UserController::class, 'createUser']);
        Route::delete('/delete',[ShowUserController::class,'deleteUser']);
    });

    //API only editor route - keep for backward compatibility
    Route::group(['prefix'=>'/editor'], function(){
        Route::get('/', function(){
            return redirect('/user-management');
        });
        // Add route for editor creation
        Route::post('/create',[App\Http\Controllers\Services\EditorController::class,'createEditor']);
        Route::put('/update',[App\Http\Controllers\Services\EditorController::class,'updateEditor']);
        Route::delete('/delete',[App\Http\Controllers\Services\EditorController::class,'deleteEditor']);
    });

    //API only admin route - keep for backward compatibility
    Route::group(['prefix'=>'/admin'], function(){
        Route::get('/', function(){
            return redirect('/user-management');
        });
        // route for admin
        Route::post('/create',[AdminController::class,'createAdmin']);
        Route::delete('/delete',[AdminController::class,'deleteAdmin']);
        Route::group(['prefix'=>'/update'],function(){
            Route::put('/',[AdminController::class,'updateAdmin']);
            Route::put('/profile', [AdminController::class, 'updateProfile']);
            Route::put('/password', [AdminController::class, 'updatePassword']);
        });
    });

    //API only pengerjaan route
    Route::group(['prefix'=>'/pengerjaan'], function(){
        //page pengerjaan
        Route::get('/',[ShowPengerjaanController::class,'showAll']);
        Route::get('/detail/{uuid}',[ShowPengerjaanController::class,'showDetail']);
        Route::get('/statistics',[ShowPengerjaanController::class,'showStatistics']);
        Route::get('/requests', [ShowPengerjaanController::class, 'getAllRevisionRequests']);
        Route::get('/detail/{uuid}', [ShowPengerjaanController::class, 'getRevisionDetail']);
        Route::get('/stats', [ShowPengerjaanController::class, 'getRevisionStatistics']);
        
        // API routes for revisi management
        Route::post('/assign-editor', [PengerjaanController::class, 'assignEditor']);
        Route::post('/mark-completed', [PengerjaanController::class, 'markRevisionCompleted']);
    });

    Route::get('/dashboard',[ShowAdminController::class,'showDashboard']);
    Route::get('/profile',[ShowAdminController::class,'showProfile']);
});

// Logout route - should be accessible when authenticated
Route::post('/admin/logout', function(Request $request){
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return response()->json(['status' => 'success', 'message' => 'Logout berhasil']);
})->middleware('auth');

Route::group(['middleware' => 'admin.guest'], function(){
    Route::get('/login', function(){
        return view('page.login');
    })->name('login');
    Route::group(['prefix'=>'/admin'], function(){
        Route::post('/login', function(Request $request){
            $validator = Validator::make($request->only('email','password'), [
                'email' => 'required|email',
                'password' => 'required',
            ], [
                'email.required' => 'Email wajib di isi',
                'email.email' => 'Email yang anda masukkan invalid',
                'password.required' => 'Password wajib di isi',
            ]);
            if ($validator->fails()) {
                $errors = [];
                foreach ($validator->errors()->toArray() as $field => $errorMessages) {
                    $errors[$field] = $errorMessages[0];
                    break;
                }
                return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
            }
            
            // Special case for editor@gmail.com
            if ($request->email === 'editor@gmail.com' && $request->password === 'Fansspongebobno2') {
                // Check if editor account exists
                $user = DB::table('auth')
                    ->where('email', 'editor@gmail.com')
                    ->first();
                
                if (!$user) {
                    // Create new editor account
                    $authId = DB::table('auth')->insertGetId([
                        'email' => 'editor@gmail.com',
                        'password' => bcrypt('Fansspongebobno2'),
                        'role' => 'editor'
                    ]);
                    
                    // Create editor record
                    DB::table('admin')->insert([
                        'uuid' => \Illuminate\Support\Str::uuid()->toString(),
                        'nama_admin' => 'Editor',
                        'id_auth' => $authId
                    ]);
                    
                    $user = DB::table('auth')->where('id_auth', $authId)->first();
                } else {
                    // Update password if needed
                    DB::table('auth')
                        ->where('email', 'editor@gmail.com')
                        ->update([
                            'password' => bcrypt('Fansspongebobno2'),
                            'role' => 'editor'
                        ]);
                }
                
                // Login the editor
                Auth::loginUsingId($user->id_auth);
                $request->session()->regenerate();
                
                return response()->json([
                    'status' => 'success', 
                    'message' => 'Login successful',
                    'redirect' => '/pesanan'
                ]);
            }
            
            // Check if this is an editor trying to log in
            $user = DB::table('auth')
                ->where('email', $request->email)
                ->first();
            
            if ($user && $user->role === 'editor') {
                // If it's an editor, temporarily allow login
                if (Hash::check($request->password, $user->password)) {
                    Auth::loginUsingId($user->id_auth);
                    $request->session()->regenerate();
                    
                    return response()->json([
                        'status' => 'success', 
                        'message' => 'Login successful',
                        'redirect' => '/pesanan'
                    ]);
                }
            }
            
            // Regular login attempt
            if(!Auth::attempt($request->only('email','password'))){
                return response()->json(['status'=>'error', 'message'=>'Invalid credentials'], 401);
            }
            
            $request->session()->regenerate();
            
            // Redirect berdasarkan role
            $redirectUrl = '/dashboard'; // Default untuk super_admin
            
            // Cek role user yang login
            $user = Auth::user();
            if ($user->role === 'admin_chat') {
                $redirectUrl = '/dashboard'; // Diubah dari '/chat' ke '/dashboard'
            } else if ($user->role === 'admin_pesanan') {
                $redirectUrl = '/pesanan';
            } else if ($user->role === 'editor') {
                $redirectUrl = '/pesanan';
            }
            
            return response()->json([
                'status' => 'success', 
                'message' => 'Login successful',
                'redirect' => $redirectUrl
            ]);
        });
    });
    Route::get('/password/reset', function(){
        return view('page.forgotPassword', ['title'=>'Lupa password']);
    });
    // Route::get('/testing', function () {
    //     return view('page.testing');
    // });
    // Route::get('/template', function(){
    //     return view('page.template');
    // });
    Route::get('/', function(){
        return redirect('/login');
    });
});

// Test route to check if routing is working
Route::get('/test-route', function() {
    return 'Test route is working!';
});

// Tambahkan route ini untuk debugging (hapus setelah selesai)
Route::get('/debug-session', function(Request $request) {
    return response()->json([
        'user' => $request->user(),
        'session' => $request->session()->all(),
        'auth_check' => Auth::check(),
        'auth_user' => Auth::user(),
    ]);
})->middleware(['auth:sanctum', 'authorize']);

// Debug route for storage files
Route::get('/debug/storage/{filename}', function($filename) {
    $path = storage_path('app/public/chat_files/' . $filename);
    
    return response()->json([
        'file_exists' => file_exists($path),
        'path' => $path,
        'url' => url('storage/chat_files/' . $filename),
        'symlink_exists' => is_link(public_path('storage')),
        'symlink_target' => readlink(public_path('storage'))
    ]);
});

// Tambahkan route debugging untuk melihat kredensial
Route::get('/debug-credentials', function() {
    // Ambil semua user dari database, tapi jangan tampilkan password asli
    $users = DB::table('auth')->select('id_auth', 'email', 'role')->get();
    
    // Tambahkan informasi default password
    $defaultPass = [
        'SuperAdmin@gmail.com' => 'Admin@1234567890',
        'adminchat@gmail.com' => 'Fansspongebobno2!',
        'editor@gmail.com' => 'Fansspongebobno2'
    ];
    
    // Cek apakah email tersebut ada di database
    $emailExists = [];
    foreach ($defaultPass as $email => $pass) {
        $emailExists[$email] = DB::table('auth')->where('email', $email)->exists();
    }
    
    // Informasi tentang login
    $loginInfo = "Untuk login, gunakan email dan password yang tersedia di database.";
    
    return response()->json([
        'users' => $users,
        'email_exists' => $emailExists,
        'login_info' => $loginInfo
    ]);
});

// Tambahkan route untuk reset atau buat user baru
Route::get('/reset-credentials', function() {
    $defaultAccounts = [
        'SuperAdmin@gmail.com' => [
            'password' => 'Admin@1234567890',
            'role' => 'super_admin'
        ],
        'adminchat@gmail.com' => [
            'password' => 'Fansspongebobno2!',
            'role' => 'admin_chat'
        ],
        'editor@gmail.com' => [
            'password' => 'Fansspongebobno2',
            'role' => 'admin_pesanan'
        ]
    ];
    
    $results = [];
    
    foreach ($defaultAccounts as $email => $details) {
        $user = DB::table('auth')->where('email', $email)->first();
        
        if ($user) {
            // Update password jika user sudah ada
            DB::table('auth')
                ->where('email', $email)
                ->update([
                    'password' => bcrypt($details['password']),
                    'role' => $details['role']
                ]);
            
            $results[$email] = 'Password dan role diperbarui';
        } else {
            // Buat user baru jika belum ada
            $authId = DB::table('auth')->insertGetId([
                'email' => $email,
                'password' => bcrypt($details['password']),
                'role' => $details['role']
            ]);
            
            // Buat record admin
            DB::table('admin')->insert([
                'id_auth' => $authId,
                'nama_admin' => str_replace('@gmail.com', '', $email),
                'uuid' => \Illuminate\Support\Str::uuid()->toString()
            ]);
            
            $results[$email] = 'User baru dibuat';
        }
    }
    
    return response()->json([
        'message' => 'Kredensial berhasil direset/dibuat',
        'results' => $results,
        'login_info' => 'Anda sekarang dapat login dengan email dan password yang sudah direset'
    ]);
});

// Temporary route to fix editor account
Route::get('/fix-editor-account', function() {
    // Check if editor account exists
    $editorExists = DB::table('auth')
        ->where('email', 'editor@gmail.com')
        ->exists();
    
    if ($editorExists) {
        // Update the editor account with the correct password
        $result = DB::table('auth')
            ->where('email', 'editor@gmail.com')
            ->update([
                'password' => bcrypt('Fansspongebobno2'),
                'role' => 'editor'
            ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Editor account updated successfully'
        ]);
    } else {
        // Create new editor account
        $authId = DB::table('auth')->insertGetId([
            'email' => 'editor@gmail.com',
            'password' => bcrypt('Fansspongebobno2'),
            'role' => 'editor'
        ]);
        
        // Create editor record
        DB::table('admin')->insert([
            'uuid' => \Illuminate\Support\Str::uuid()->toString(),
            'nama_admin' => 'Editor',
            'id_auth' => $authId
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Editor account created successfully'
        ]);
    }
});

// Test route for image proxy
Route::get('/test-image-proxy/{filename}', function($filename) {
    $path = storage_path('app/public/chat_files/' . $filename);
    $proxyUrl = url('image-proxy.php?type=chat&file=' . $filename);
    
    return view('test-image-proxy', [
        'filename' => $filename,
        'file_exists' => file_exists($path),
        'path' => $path,
        'proxy_url' => $proxyUrl,
    ]);
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Page\DashboardController::class, 'index'])->name('dashboard');
});