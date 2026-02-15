<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfGuest
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            // Redirect all protected routes to your portal login
            return redirect()->route('portal.login');
        }

        return $next($request);
    }
}
