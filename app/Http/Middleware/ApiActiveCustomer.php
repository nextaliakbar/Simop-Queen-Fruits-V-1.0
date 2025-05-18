<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ApiActiveCustomer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (Auth::user()->is_active == 1) {
            return $next($request);
        }
        $errors = [];
        $errors[] = ['code' => 'auth-001', 'message' => 'Unauthenticated.'];
        return response()->json([
            'errors' => $errors
        ], 401);
    }
}
