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
    private $roleAdmin = ['super_admin','admin'];
    public function handle(Request $request, Closure $next){
        // Temporarily allow all requests to metode-pembayaran routes
        if (Str::startsWith($request->path(), 'metode-pembayaran')) {
            return $next($request);
        }
        
        $path = '/'.$request->path();
        $role = $request->user()['role'];
        //only admin can access admin feature
        if(in_array($role, ['user']) && !Str::startsWith($path, ['/api/mobile'])){
            return response()->json(['status'=>'error','message'=>'User Unauthorized'],403);
        }
        
        //only user can't access /chat, allow all admin types
        if($role === 'user' && (Str::startsWith($path, ['/chat']))){
            return response()->json(['status'=>'error','message'=>'User Unauthorized'],403);
        }
        
        //only user can't access /jasa or /pesanan, allow all admin types
        if($role === 'user' && (Str::startsWith($path, ['/jasa', '/pesanan']))){
            return response()->json(['status'=>'error','message'=>'User Unauthorized'],403);
        }
        
        //only super admin can access /admin
        if(in_array($role,['admin', 'user']) && Str::startsWith($path, '/admin')){
            return response()->json(['status'=>'error','message'=>'User Unauthorized'],403);
        }
        //when admin access mobile
        if(in_array($role, $this->roleAdmin) && Str::startsWith($path, '/mobile')){
            return response()->json(['status'=>'error','message'=>'User Unauthorized'],403);
        }
        return $next($request);
    }
}