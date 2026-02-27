<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Core1RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Explicitly check the 'core1' guard
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('core1.login');
        }

        // Check if user has one of the required roles
        if (!in_array($user->role_slug, $roles)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}

