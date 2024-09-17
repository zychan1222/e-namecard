<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendTACRequest;
use App\Http\Requests\LoginWithTACRequest;
use App\Services\LoginService;
use App\Models\User;
use App\Models\UserOrganization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    protected $loginService;

    public function __construct(LoginService $loginService)
    {
        $this->loginService = $loginService;
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function sendTAC(SendTACRequest $request)
    {
        $email = $request->input('email');

        if ($this->loginService->sendTAC($email)) {
            return redirect()->route('login.tac.show', ['email' => $email])
                             ->with('success', 'TAC sent successfully to your email.');
        }

        return redirect()->route('login')->withErrors(['email' => 'No user found with this email.']);
    }

    public function showTACForm(Request $request, $email)
    {
        return view('auth.tac', ['email' => $email]);
    }

    public function loginWithTAC(LoginWithTACRequest $request)
    {
        $email = $request->input('email');
        $tacCode = $request->input('tac_code');

        if ($this->loginService->verifyTAC($email, $tacCode)) {
            $user = $this->loginService->getUserByEmail($email);

            if ($user) {
                return redirect()->route('select.organization', ['email' => $email]);
            }
        }

        return redirect()->route('login.tac.show', ['email' => $email])
                         ->withErrors(['tac_code' => 'Invalid TAC code or it has expired.']);
    }

    public function showOrganizationSelectionForm(Request $request)
    {
        $email = $request->input('email');
    
        $users = User::where('email', $email)->get();
    
        // Retrieve organizations associated with the users
        $userOrganizations = UserOrganization::whereIn('user_id', $users->pluck('id'))
            ->with('organization')
            ->get();
    
        return view('auth.select_organization', ['userOrganizations' => $userOrganizations]);
    }

    public function selectOrganization(Request $request)
    {
        Log::info('Incoming Request Data:', $request->all());

        $userId = $request->input('user_id');
        $organizationId = $request->input('organization_id');

        $userOrganization = UserOrganization::where('user_id', $userId)
                                            ->where('organization_id', $organizationId)
                                            ->first();

        if ($userOrganization) {
            $user = $this->loginService->authenticateUser($userId);

            if ($user) {
                $organization = $userOrganization->organization;

                Log::info('Organization found:', ['organization_name' => $organization->name]);

                return redirect()->route('dashboard')
                                 ->with('success', 'Logged in and organization selected successfully.')
                                 ->with('organization', $organization);
            } else {
                Log::error('User not found:', ['user_id' => $userId]);
            }
        } else {
            Log::error('UserOrganization not found:', ['user_id' => $userId, 'organization_id' => $organizationId]);
        }

        return redirect()->route('select.organization')
                         ->withErrors(['organization_id' => 'Invalid selection or organization.']);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/')->with('success', 'Successfully logged out.');
    }
}