<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            $currentRoute = $request->route()->getName();
            $employeeId = session('employee_id');
            $userId = Auth::id();

            Log::info('RedirectIfAuthenticated middleware triggered.', [
                'current_route' => $currentRoute,
                'user_id' => $userId,
                'employee_id' => $employeeId,
            ]);

            // Allow access to the organization selection page and related POST request
            if ($currentRoute === 'select.organization' || $currentRoute === 'select.organization.post') {
                return $next($request);
            }

            // Redirect to the organization selection page if no employee ID is in session
            if (!$employeeId) {
                Log::info('No employee ID in session. Redirecting to organization selection.');
                return redirect()->route('select.organization');
            }

            // If already on the dashboard, continue the request
            return $next($request);
        }

        return $next($request);
    }
}
