<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserOrganization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function showDashboard()
    {
        if (!$this->isAuthenticated()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        $userOrg = UserOrganization::where('user_id', $user->id)
            ->first();

        if (!$userOrg) {
            Log::warning('User is not associated with any organization.', ['user_id' => $user->id]);
            return redirect()->route('login')->withErrors(['error' => 'User is not associated with any organization.']);
        }

        $organization = $userOrg->organization;
        $pageTitle = 'Dashboard';

        return view('dashboard', compact('user', 'userOrg', 'organization', 'pageTitle'));
    }

    protected function isAuthenticated()
    {
        return Auth::check();
    }
}
