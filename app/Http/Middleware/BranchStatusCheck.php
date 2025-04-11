<?php

namespace App\Http\Middleware;

use Brian2694\Toastr\Facades\Toastr;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BranchStatusCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(auth('branch')->user()->status == 1) {
            return $next($request);   
        }
        auth()->guard('branch')->logout();
        Toastr::warning('Akun dinonaktifkan');
        return redirect()->route('branch.auth.login');
    }
}
