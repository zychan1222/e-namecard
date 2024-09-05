<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;

class LogSessionData
{
    public function handle($request, Closure $next)
    {
        // Log session data
        Log::info('Session data after redirect:', [
            'session_data' => $request->session()->all(),
        ]);

        return $next($request);
    }
}
