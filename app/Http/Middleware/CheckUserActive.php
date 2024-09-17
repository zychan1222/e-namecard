<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->is_active) {
            return $next($request);
        }

        // If user is not active, redirect with an error message
        return redirect('/login')->withErrors(['error' => 'Your account is not active. Please contact support.']);
    }
}
