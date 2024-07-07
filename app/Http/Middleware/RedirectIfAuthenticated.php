<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            Log::info("Checking guard: $guard");
            if (Auth::guard($guard)->check()) {
                Log::info("User authenticated with guard: $guard");

                return redirect('/dashboard');
            }
        }

        return $next($request);
    }
}
