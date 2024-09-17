<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserOrganization;

class CheckAdminRole
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user) {
            // Eager load the organization relationship
            $userOrg = UserOrganization::with('organization')
                ->where('user_id', $user->id)
                ->first();
                
            if ($userOrg && $userOrg->role_id == 3) {
                // Redirect back with an error message if role_id is 3
                return back()->withErrors(['role' => 'You do not have permission to access this page.']);
            }
        }

        return $next($request);
    }
}
