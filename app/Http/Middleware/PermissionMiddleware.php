<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{   
    // Handle an incoming request.
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = Auth::user();

        if (!$user || !$user->role->permissions->contains('label', $permission)) {
            return response()->json(['message' => 'You do not have permission for this action'], 403);
        }

        return $next($request);
    }
}
