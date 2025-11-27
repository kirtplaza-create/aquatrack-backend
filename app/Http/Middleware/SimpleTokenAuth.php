<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SimpleTokenAuth
{
    public function handle(Request $request, Closure $next)
    {
        $header = $request->header('Authorization'); // expects "Bearer <token>"

        if (! $header || ! str_starts_with($header, 'Bearer ')) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // For this project we just require some Bearer token (issued by /login)
        return $next($request);
    }
}
