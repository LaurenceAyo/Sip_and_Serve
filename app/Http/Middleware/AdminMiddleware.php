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

        if (Auth::user()->email !== 'laurenceayo7@gmail.com') {
            return redirect()->route('dashboard')->with('error', 'Access denied');
        }

        return $next($request);
    }
}