<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || $user->role !== 'admin') {
            abort(403);
        }

        if ($user->is_active === false) {
            auth()->logout();
            return redirect('/login')->with('error', 'Your account has been deactivated. Please contact the Super Admin.');
        }

        return $next($request);
    }
}
