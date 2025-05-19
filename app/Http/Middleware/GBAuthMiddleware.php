<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class GBAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$guards): Response
    {
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $requiredRole = $guards[0] ?? null;
        $requiredPermission = $guards[1] ?? null;

//        if ($requiredRole == 'admin' && $user->role != 'admin') {
//            return response()->json(['message' => 'Forbidden'], 403);
//        }

//        if ($requiredPermission && !$user->hasPermission($requiredPermission)) {
//            return response()->json(['message' => 'Forbidden'], 403);
//        }

        return $next($request);
    }
}
