<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequirePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->must_change_password) {
            // Allow the forced-change route and logout through
            if ($request->routeIs('admin.password.force.show') ||
                $request->routeIs('admin.password.force.update') ||
                $request->routeIs('logout')) {
                return $next($request);
            }

            return redirect()->route('admin.password.force.show')
                ->with('warning', 'You must set a new password before continuing.');
        }

        return $next($request);
    }
}
