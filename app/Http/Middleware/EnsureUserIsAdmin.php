<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'forbidden',
                    'message' => 'Unauthorized action. Administrator rights required.',
                ], 403);
            }
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}