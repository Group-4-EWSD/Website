<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is already logged in
        if (Auth::check()) {
            return response()->json([
                'status' => 200,
                'message' => 'Already logged in',
                'redirect' => url('/api/dashboard') // Change this to your dashboard API
            ]);
        }

        return $next($request);
    }
}
