<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAdminAuthenticated
{
    public function handle(Request $request, Closure $next, string $guard = 'admin'): Response
    {
        Log::info("Checking admin guard: $guard");
        if (Auth::guard($guard)->check()) {
            Log::info("Admin user authenticated with guard: $guard");
            return redirect('/admin/dashboard');
        }

        return $next($request);
    }
}