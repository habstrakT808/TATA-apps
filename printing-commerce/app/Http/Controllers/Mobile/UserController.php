<?php
namespace App\Http\Controllers\Mobile;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth as LaravelAuth;
use App\Models\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\UtilityController;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
class UserController extends Controller
{
    /**
     * Mobile app login - generates API token for Sanctum authentication
     */
    public function login(Request $request){
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
        
        $auth = Auth::where('email', $request->input('email'))->first();
        if(!$auth || !Hash::check($request->input('password'), $auth->password)){
            return response()->json(['status' => 'error', 'message' => 'Invalid Credentials'], 401);
        }
        
        // Get user details
        $user = User::where('id_auth', $auth->id_auth)->first();
        
        // Create token with abilities for mobile app
        $token = $auth->createToken('mobile-auth-token', ['mobile-access'])->plainTextToken;
        
        // Return token with user info
        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil',
            'data' => [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->uuid,
                    'name' => $user->nama_user,
                    'email' => $auth->email,
                    'role' => $auth->role,
                    'alamat' => $user->alamat,
                'no_telpon' => $user->no_telpon,
                'foto' => $user->foto
                ]
            ]
        ]);
    }
    public function logingoogle(Request $request){
        $validator = Validator::make($request->only('email'), [
            'email' => 'required|email',
        ], [
            'email.required' => 'Email wajib di isi',
            'email.email' => 'Email yang anda masukkan invalid',
        ]);
        
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->toArray() as $field => $errorMessages) {
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }
        
        $auth = Auth::where('email', $request->input('email'))->first();
        if(!$auth){
            return response()->json(['status' => 'error','note' => $auth, 'message' => 'Invalid Credentials'], 401);
        }
        
        // Get user details
        $user = User::where('id_auth', $auth->id_auth)->first();
        
        // Create token with abilities for mobile app
        $token = $auth->createToken('mobile-auth-token', ['mobile-access'])->plainTextToken;
        
        // Return token with user info
        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil',
            'data' => [
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->uuid,
                    'name' => $user->nama_user,
                    'email' => $auth->email,
                    'role' => $auth->role,
                    'alamat' => $user->alamat,
                'no_telpon' => $user->no_telpon,
                'foto' => $user->foto
                ]
            ]
        ]);
    }
  
  
  
    // Fungsi untuk memeriksa ketersediaan email (untuk registrasi)
    public function checkEmail(Request $request){
        $validator = Validator::make($request->only('email'), [
            'email' => 'required|email',
        ], [
            'email.required' => 'Email wajib di isi',
        ]);
        
        if ($validator->fails()) {
            $errors = [];
            foreach ($validator->errors()->toArray() as $field => $errorMessages) {
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }
        
         if(Auth::select("email")->whereRaw("BINARY email = ?",[$request->input('email')])->exists()){
            return response()->json(['status'=>'error','message'=>'Email sudah digunakan'],400);
        }else{
            return response()->json(['status'=>'success','message'=>'Silahkan Lanjutkan'],200);
        }
    }
     
    // Menjaga backward compatibility dengan kode lama
    public function CekEmail(Request $request){
        return $this->checkEmail($request);
    }
public function changePassEmail(Request $request)
{
    $validator = Validator::make($request->only('email', 'password'), [
        'email' => 'required|email',
        'password' =>'required'
    ], [
        'email.required' => 'Email wajib diisi',
        'email.email' => 'Format email tidak valid',
        'password.required' => 'Password wajib diisi',
        'password.min' => 'Password minimal 8 karakter',
        'password.max' => 'Password maksimal 25 karakter',
        
    ]);

    if ($validator->fails()) {
        $errors = [];
        foreach ($validator->errors()->toArray() as $field => $errorMessages) {
            $errors[$field] = $errorMessages[0];
            break;
        }
        return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
    }

    $auth = Auth::whereRaw("BINARY email = ?", [$request->email])->first();
    if (!$auth) {
        return response()->json(['status' => 'error', 'message' => 'Email tidak ditemukan'], 404);
    }

    $auth->password = Hash::make($request->password);
    $auth->save();

    return response()->json(['status' => 'success', 'message' => 'Password berhasil diperbarui']);
}

public function updateProfile(Request $request)
{
    $auth = auth()->user();

    if (!$auth) {
        return response()->json([
            'message' => 'User tidak terautentikasi'
        ], 401);
    }

    // Validasi
    $validator = Validator::make($request->all(), [
        'nama_user' => 'required|string|max:50',
        'no_telpon' => 'nullable|string|max:20',
        'alamat' => 'nullable|string',
        'email' => 'required|email|max:255|unique:auth,email,' . $auth->id_auth . ',id_auth',
        'foto' => 'nullable|file|image|mimes:jpeg,png,jpg|max:2048',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Validasi gagal',
            'errors' => $validator->errors()
        ], 422);
    }

    // Update email
    $auth->email = $request->email;
    $auth->save();

    $user = User::where('id_auth', $auth->id_auth)->first();
    if (!$user) {
        return response()->json(['message' => 'User profile tidak ditemukan'], 404);
    }
if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
    $file = $request->file('foto');

    // Buat nama file unik secara manual
    $hashedName = uniqid('user_') . '.' . $file->getClientOriginalExtension();

    // Path ke folder berdasarkan lingkungan
    $destinationPath = env('APP_ENV') === 'production' 
        ? base_path('../public_html/assets3/img/user')
        : public_path('assets3/img/user');

    // Buat folder jika belum ada
    if (!file_exists($destinationPath)) {
        mkdir($destinationPath, 0777, true);
    }

    // Hapus foto lama jika ada
    if ($user->foto && file_exists($destinationPath . '/' . $user->foto)) {
        @unlink($destinationPath . '/' . $user->foto);
    }

    // Pindahkan file ke folder tujuan dengan nama yang sesuai
    $file->move($destinationPath, $hashedName);

    // Simpan nama file ke database
    $user->foto = $hashedName;
    
    // Debug info
    \Log::info('Foto profil berhasil diupload', [
        'filename' => $hashedName,
        'path' => $destinationPath,
        'full_path' => $destinationPath . '/' . $hashedName,
        'exists' => file_exists($destinationPath . '/' . $hashedName)
    ]);
}

    // Update data user
    $user->nama_user = $request->nama_user;
    $user->no_telpon = $request->no_telpon;
    $user->alamat = $request->alamat;
    $user->save();

    // Generate token baru
    $token = $auth->createToken('mobile-auth-token', ['mobile-access'])->plainTextToken;

    return response()->json([
        'message' => 'Berhasil update data',
        'data' => [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->uuid,
                'name' => $user->nama_user,
                'email' => $auth->email,
                'role' => $auth->role,
                'alamat' => $user->alamat,
                'no_telpon' => $user->no_telpon,
                'foto' => $user->foto,
            ],
        ],
    ], 200);
}

  public function register(Request $request){
        $validator = Validator::make($request->only('email', 'nama_user','no_telpon','password', 'password_confirmation'), [
            'email'=>'required|email',
            'nama_user' => 'required|min:3|max:50',
            'no_telpon' => 'required|max:15',
            'password' => [
                'required',
                'string',
                'min:8',
                'max:25',
            ],
            'password_confirmation' => 'required|same:password',
        ],[
            'email.required'=>'Email wajib di isi',
            'email.email'=>'Email yang anda masukkan invalid',
            'no_telpon.required' => 'Nomor Telepon wajib di isi',
            'nama_user.required' => 'Nama user wajib di isi',
            'nama_user.min'=>'Nama user minimal 3 karakter',
            'nama_user.max' => 'Nama user maksimal 50 karakter',
            'password.required'=>'Password wajib di isi',
            'password.min'=>'Password minimal 8 karakter',
            'password.max'=>'Password maksimal 25 karakter',
           
            'password_confirmation.required'=>'Password confirmation wajib di isi',
            'password_confirmation.same'=>'Password confirmation tidak sama dengan password',
        ]);
        if($validator->fails()){
            $errors = [];
            foreach($validator->errors()->toArray() as $field => $errorMessages){
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }
        if(Auth::select("email")->whereRaw("BINARY email = ?",[$request->input('email')])->exists()){
            return response()->json(['status'=>'error','message'=>'Email sudah digunakan'],400);
        }
        $idAuth = Auth::insertGetId([
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            // 'no_telpon' => $request->input('no_telpon'),
            'role'=> 'user',
        ]);
        $ins = User::insert([
            'uuid' =>  Str::uuid(),
            'nama_user' => $request->input('nama_user'),
            'no_telpon' => $request->input('no_telpon'),
            'id_auth' => $idAuth,
        ]);
        if(!$ins){
            return response()->json(['status'=>'error','message'=>'Gagal register'], 500);
        }
        return response()->json(['status' => 'success', 'message' => 'Register berhasil. Silahkan login untuk melanjutkan.']);
    }
    
    public function dashboard(Request $request){
        $dataShow = [
            'userAuth' => array_merge(User::where('id_auth', $request->user()['id_auth'])->first()->toArray(), ['role' => $request->user()['role']]),
        ];
        return response()->json($dataShow);
    }
    
    public function update(Request $request){
        $user = User::where('id_auth', $request->user()->id_auth)->first();
        $user->name = $request->name;
        $user->save();
        return response()->json(['status' => 'success', 'message' => 'User updated successfully']);
    }
    
    public function delete(Request $request){
        $user = User::where('id_auth', $request->user()->id_auth)->first();
        $user->delete();
        return response()->json(['status' => 'success', 'message' => 'User deleted successfully']);
    }

    //from admin
    public function createUser(Request $rt){
        $validator = Validator::make($rt->only('email', 'nama', 'no_telpon', 'password'), [
            'email'=>'required|email',
            'nama' => 'required|min:3|max:50',
            'no_telpon' => 'required|max:15',
            'password' => [
                'required',
                'string',
                'min:8',
                'max:25',
            ],
        ],[
            'email.required'=>'Email wajib di isi',
            'email.email'=>'Email yang anda masukkan invalid',
            'nama.required' => 'Nama user wajib di isi',
            'nama.min'=>'Nama user minimal 3 karakter',
            'nama.max' => 'Nama user maksimal 50 karakter',
            'password.required'=>'Password wajib di isi',
            'password.min'=>'Password minimal 8 karakter',
            'password.max'=>'Password maksimal 25 karakter',
        ]);
        
        if($validator->fails()){
            $errors = [];
            foreach($validator->errors()->toArray() as $field => $errorMessages){
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }
        
        if(Auth::select("email")->whereRaw("BINARY email = ?",[$rt->input('email')])->exists()){
            return response()->json(['status'=>'error','message'=>'Email sudah digunakan'],400);
        }
        
        $idAuth = Auth::insertGetId([
            'email' => $rt->input('email'),
            'password' => Hash::make($rt->input('password')),
            'role' => 'user',
        ]);
        
        $ins = User::insert([
            'uuid' =>  Str::uuid(),
            'nama_user' => $rt->input('nama'),
            'no_telpon' => $rt->input('no_telpon'),
            'id_auth' => $idAuth,
        ]);
        
        if(!$ins){
            // Delete auth record if user insertion failed
            Auth::where('id_auth', $idAuth)->delete();
            return response()->json(['status'=>'error','message'=>'Gagal menambahkan data User'], 500);
        }
        
        return response()->json(['status' => 'success', 'message' => 'Data User berhasil ditambahkan']);
    }
    
    //from admin
    public function deleteUser(Request $rt){
        $validator = Validator::make($rt->only('uuid'), [
            'uuid' => 'required',
        ], [
            'uuid.required' => 'User ID wajib di isi',
        ]);
        if ($validator->fails()){
            $errors = [];
            foreach ($validator->errors()->toArray() as $field => $errorMessages){
                $errors[$field] = $errorMessages[0];
                break;
            }
            return response()->json(['status' => 'error', 'message' => implode(', ', $errors)], 400);
        }
        $user = User::where('uuid', $rt->input('uuid'))->firstOrFail();
        $ftd = public_path('assets3/img/user/') . $user['foto'];
        if (file_exists($ftd) && !is_dir($ftd)){
            unlink($ftd);
        }
        if(!$user->delete()){
            return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data User'], 500);
        }
        return response()->json(['status' => 'success', 'message' => 'Data User berhasil dihapus']);
    }
    
    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response()->json(['status' => 'success', 'message' => 'Logout berhasil silahkan login kembali']);
    }
    
    public function logoutAll(Request $request){
        $request->user()->tokens()->delete();
        return response()->json(['status' => 'success', 'message' => 'Berhasil logout dari semua perangkat']);
    }
    
    /**
     * Refresh token for mobile app
     */
    public function refreshToken(Request $request)
    {
        try {
            $user = $request->user();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found for token'
                ], 401);
            }

            // Hapus semua token yang ada untuk user ini
            $user->tokens()->delete();

            // Buat token baru
            $token = $user->createToken('mobile-auth-token', ['mobile-access'])->plainTextToken;

            // Ambil data user dari tabel 'users'
            $userData = User::where('id_auth', $user->id_auth)->first();

            return response()->json([
                'status' => 'success',
                'message' => 'Token refreshed successfully',
                'data' => [
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'user' => [
                        'id' => $userData->uuid ?? null,
                        'name' => $userData->nama_user ?? null,
                        'email' => $user->email,
                        'role' => $user->role,
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error refreshing token: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to refresh token: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Firebase Authentication Login
     */
    public function firebaseLogin(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 400);
            }

            // Cari user berdasarkan email
            $auth = Auth::where('email', $request->email)->first();
            
            if (!$auth) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Email tidak terdaftar'
                ], 404);
            }

            // Verifikasi password
            if (!Hash::check($request->password, $auth->password)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Password salah'
                ], 401);
            }

            // Get user details
            $user = User::where('id_auth', $auth->id_auth)->first();
            
            // Create token with abilities for mobile app
            $token = $auth->createToken('firebase-auth-token', ['mobile-access'])->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Login berhasil',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->uuid,
                    'name' => $user->nama_user,
                    'email' => $auth->email,
                    'role' => $auth->role,
                    'alamat' => $user->alamat,
                    'no_telpon' => $user->no_telpon,
                    'foto' => $user->foto
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Firebase Authentication Register
     */
    public function firebaseRegister(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:auth,email',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 400);
            }

            // Generate UUID
            $uuid = (string) Str::uuid();

            // Buat auth
            $auth = new Auth();
            $auth->email = $request->email;
            $auth->password = Hash::make($request->password);
            $auth->role = 'user';
            $auth->save();

            // Buat user
            $user = new User();
            $user->id_auth = $auth->id_auth;
            $user->uuid = $uuid;
            $user->nama_user = $request->name;
            $user->save();

            // Generate token
            $token = $auth->createToken('firebase-auth-token', ['mobile-access'])->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Registrasi berhasil',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->uuid,
                    'name' => $user->nama_user,
                    'email' => $auth->email,
                    'role' => $auth->role
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Google Sign-In
     */
    public function googleSignIn(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'name' => 'required|string',
                'id_token' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 400);
            }

            // Cari user berdasarkan email
            $auth = Auth::where('email', $request->email)->first();
            
            if (!$auth) {
                // Jika user belum terdaftar, daftarkan user baru
                $uuid = (string) Str::uuid();

                // Buat auth
                $auth = new Auth();
                $auth->email = $request->email;
                $auth->password = Hash::make(Str::random(16)); // Generate random password
                $auth->role = 'user';
                $auth->save();

                // Buat user
                $user = new User();
                $user->id_auth = $auth->id_auth;
                $user->uuid = $uuid;
                $user->nama_user = $request->name;
                $user->save();
            } else {
                // Jika user sudah terdaftar, ambil data user
                $user = User::where('id_auth', $auth->id_auth)->first();
            }

            // Generate token
            $token = $auth->createToken('firebase-google-auth-token', ['mobile-access'])->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Login berhasil',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => [
                    'id' => $user->uuid,
                    'name' => $user->nama_user,
                    'email' => $auth->email,
                    'role' => $auth->role,
                    'alamat' => $user->alamat,
                    'no_telpon' => $user->no_telpon,
                    'foto' => $user->foto
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update FCM token
     */
    public function updateFCMToken(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'fcm_token' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => $validator->errors()->first()
                ], 400);
            }

            $userId = auth()->user()->id_auth;
            $auth = Auth::find($userId);
            
            if (!$auth) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            // Update FCM token
            $auth->fcm_token = $request->fcm_token;
            $auth->save();

            return response()->json([
                'status' => 'success',
                'message' => 'FCM token berhasil diperbarui',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user profile
     */
    public function getProfile(Request $request)
    {
        try {
            $auth = $request->user();
            
            if (!$auth) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User tidak terautentikasi'
                ], 401);
            }
            
            $user = User::where('id_auth', $auth->id_auth)->first();
            
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Profile user tidak ditemukan'
                ], 404);
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'Profile berhasil diambil',
                'data' => [
                    'user' => [
                        'id' => $user->uuid,
                        'name' => $user->nama_user,
                        'email' => $auth->email,
                        'role' => $auth->role,
                        'alamat' => $user->alamat,
                        'no_telpon' => $user->no_telpon,
                        'foto' => $user->foto
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting user profile: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    /**
     * Get user reviews
     */
    public function getUserReviews(Request $request)
    {
        try {
            // Return sample reviews for now
            $sampleReviews = [
                [
                    'id' => '1',
                    'name' => 'John Doe',
                    'rating' => 5,
                    'feedback' => 'Pelayanan sangat memuaskan dan hasil desain bagus!',
                    'avatar_url' => null
                ],
                [
                    'id' => '2',
                    'name' => 'Jane Smith',
                    'rating' => 4,
                    'feedback' => 'Desain sesuai dengan keinginan, pengerjaan cepat.',
                    'avatar_url' => null
                ]
            ];
            
            return response()->json([
                'status' => 'success',
                'message' => 'Review berhasil diambil',
                'data' => $sampleReviews
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting user reviews: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }
}