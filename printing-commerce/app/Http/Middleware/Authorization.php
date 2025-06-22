<?php
namespace App\Http\Middleware;
use Illuminate\Support\Str;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\JWTController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Response;
use App\Models\User;
use Closure;
class Authorization
{
    // Definisikan akses untuk setiap role
    private $roleAccess = [
        'super_admin' => [
            '/dashboard',
            '/jasa',
            '/payment-methods',
            '/user-management',
            '/admin',
            '/profile',
            '/editor',
            '/user',
        ],
        'admin_chat' => [
            '/chat',
            '/profile',
            '/dashboard',
        ],
        'admin_pesanan' => [
            '/pesanan',
            '/profile',
        ],
        'editor' => [
            '/pesanan',
            '/profile',
            '/pengerjaan',
        ],
    ];
    
    public function handle(Request $request, Closure $next){
        // Temporarily allow all requests to metode-pembayaran routes
        if (Str::startsWith($request->path(), 'metode-pembayaran')) {
            return $next($request);
        }
        
        $path = '/'.$request->path();
        $role = $request->user()['role'];

        // Jika user biasa, tidak diperbolehkan mengakses halaman admin
        if($role === 'user' && !Str::startsWith($path, ['/api/mobile'])){
            return response()->json(['status'=>'error','message'=>'User Unauthorized'],403);
        }
        
        // Special case for editor delete functionality
        if ($role === 'super_admin' && $request->is('editor/delete')) {
            return $next($request);
        }
        
        // Jika admin, periksa apakah mereka memiliki akses ke path yang diminta
        if(in_array($role, array_keys($this->roleAccess))) {
            $hasAccess = false;
            
            foreach($this->roleAccess[$role] as $allowedPath) {
                if(Str::startsWith($path, $allowedPath)) {
                    $hasAccess = true;
                    break;
                }
            }
            
            // Jika tidak memiliki akses, kembalikan response 403
            if(!$hasAccess) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Anda tidak memiliki akses ke halaman ini'
                ], 403);
            }
        }
        
        // Jika admin mencoba mengakses mobile API
        if(in_array($role, array_keys($this->roleAccess)) && Str::startsWith($path, '/mobile')){
            return response()->json(['status'=>'error','message'=>'User Unauthorized'],403);
        }
        
        return $next($request);
    }
}