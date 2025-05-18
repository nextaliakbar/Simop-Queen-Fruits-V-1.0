<?php

namespace App\Http\Middleware;

use App\Models\Branch;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class BranchAddrer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        Config::set('branch_id', $request->header('branch-id') );

        $branch = Branch::where('id', $request->header('branch_id'))->first();
        if (!isset($branch)) {
            $errors = [];
            $errors[] = ['code' => 'auth-001', 'message' => 'Branch not match.'];
            return response()->json(['errors' => $errors], 401);
        }

        return $next($request);
    }
}
