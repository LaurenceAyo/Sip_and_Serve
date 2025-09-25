<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if (!$user->canAccessAdmin()) {
            session(['error' => 'Administrator access required']);
            return redirect()->route('unauthorized');
        }

        return $next($request);
    }
}