<?php
namespace App\Http\Middleware;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Closure;
class RedirectIfAuthenticatedAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if(Auth::check()){
            return redirect('/dashboard');
        }
        return $next($request);
    }
}