<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{   
    // Handle an incoming request.
    public function handle(Request $request, Closure $next, $role)
    {
        $user = Auth::user();

        if (!$user || $user->role->name !== $role) {
            return response()->json(['message' => 'Your role does not have access to this resource'], 403);
        }

        return $next($request);
    }
}
