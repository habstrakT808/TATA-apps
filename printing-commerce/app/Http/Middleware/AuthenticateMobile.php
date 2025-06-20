<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticateMobile
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('AuthenticateMobile middleware called', [
            'path' => $request->path(),
            'method' => $request->method(),
            'has_bearer_token' => $request->bearerToken() ? 'yes' : 'no',
            'headers' => $request->headers->all()
        ]);

        if (!$request->bearerToken()) {
            Log::warning('No bearer token provided');
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated. Please login to continue.',
                'debug' => 'No bearer token found in request'
            ], 401);
        }

        try {
            // Check if token is valid
            if (!Auth::guard('sanctum')->check()) {
                // Log token info for debugging (only first few characters for security)
                $token = $request->bearerToken();
                $tokenPreview = substr($token, 0, 10) . '...';
                
                Log::warning('Invalid token provided', [
                    'token_preview' => $tokenPreview,
                    'token_length' => strlen($token)
                ]);
                
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid token. Please login again.',
                    'debug' => 'Token validation failed'
                ], 401);
            }

            $user = Auth::guard('sanctum')->user();
            if (!$user) {
                Log::warning('User not found for token');
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found. Please login again.',
                    'debug' => 'Token valid but user not found'
                ], 401);
            }

            Log::info('Authentication successful', [
                'user_id' => $user->id,
                'user_email' => $user->email
            ]);
            
            return $next($request);
        } catch (\Exception $e) {
            Log::error('Exception in AuthenticateMobile middleware: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Authentication error: ' . $e->getMessage(),
                'debug' => 'Exception occurred during authentication'
            ], 500);
        }
    }
} 