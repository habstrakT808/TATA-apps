<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class AuthenticateMobile extends Middleware
{
    /**
     * Handle an incoming request for mobile API.
     */
    protected function authenticate($request, array $guards)
    {
        if ($this->auth->guard('sanctum')->check()) {
            return $this->auth->shouldUse('sanctum');
        }

        $this->unauthenticated($request, ['sanctum']);
    }

    /**
     * Handle unauthenticated requests from mobile app - always return JSON
     */
    protected function redirectTo($request)
    {
        // Always return null to prevent redirects
        return null;
    }

    /**
     * Handle unauthenticated requests from mobile app
     */
    protected function unauthenticated($request, array $guards)
    {
        abort(response()->json([
            'status' => 'error',
            'message' => 'Unauthenticated. Please login to continue.',
        ], 401));
    }
} 