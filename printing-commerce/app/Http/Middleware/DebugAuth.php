<?php
// app/Http/Middleware/DebugAuth.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DebugAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Hanya untuk debugging, bisa dihapus di production
        if (env('APP_DEBUG', false)) {
            Log::info('Debug Auth Middleware', [
                'url' => $request->url(),
                'method' => $request->method(),
                'headers' => $request->headers->all(),
                'user_authenticated' => auth('sanctum')->check(),
                'user_id' => auth('sanctum')->id(),
            ]);
        }
        
        return $next($request);
    }
} 