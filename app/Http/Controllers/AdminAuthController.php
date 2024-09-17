<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendTACRequest;
use App\Http\Requests\LoginWithTACRequest;
use App\Services\AdminAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    protected $adminAuthService;

    public function __construct(AdminAuthService $adminAuthService)
    {
        $this->adminAuthService = $adminAuthService;
    }

    public function showAdminLoginForm()
    {
        return view('auth.admin-login');
    }

    public function sendTAC(SendTACRequest $request)
    {
        $email = $request->input('email');

        if ($this->adminAuthService->sendTAC($email)) {
            return redirect()->route('admin.login.tac.show', ['email' => $email])->with('success', 'TAC sent successfully to your email.');
        }

        return redirect()->route('admin.login.form')->withErrors(['email' => 'No user found with this email.']);
    }

    public function showTACForm(Request $request, $email)
    {
        return view('auth.admin-tac', ['email' => $email]);
    }

    public function loginWithTAC(LoginWithTACRequest $request)
    {
        $email = $request->input('email');
        $tacCode = $request->input('tac_code');

        if ($this->adminAuthService->verifyTAC($email, $tacCode)) {

            $users = $this->adminAuthService->getUserOrganizations([$email]);

            return redirect()->route('admin.select.organization', ['email' => $email]);
        }

        return redirect()->route('admin.login.tac.show', ['email' => $email])
            ->withErrors(['tac_code' => 'Invalid TAC code or it has expired.']);
    }

    public function showOrganizationSelectionForm(Request $request)
    {
        $email = $request->input('email');
        $users = $this->adminAuthService->getUserOrganizations([$email]);

        return view('auth.admin-select_organization', ['userOrganizations' => $users]);
    }
    public function selectOrganization(Request $request)
    {
        $userId = $request->input('user_id');
        $organizationId = $request->input('organization_id');
    
        $userOrganization = $this->adminAuthService->findUserOrganization($userId, $organizationId);
    
        if ($userOrganization) {
    
            if ($userOrganization->role_id == 3) {
                return redirect()->route('admin.login.form')
                    ->withErrors(['role' => 'You do not have permission to access this organization.']);
            }
    
            $user = $this->adminAuthService->loginUser($userId);
    
            if ($user) {
                $organization = $userOrganization->organization;
    
                if ($organization) {
                    return redirect()->route('admin.dashboard')
                        ->with('success', 'Logged in and organization selected successfully.')
                        ->with('organization', $organization);
                } else {
                    // Handle case where the organization is not found
                    return redirect()->route('admin.select.organization')
                        ->withErrors(['organization_id' => 'Organization not found.']);
                }
            } else {
                // Handle case where the user is not found
                return redirect()->route('admin.select.organization')
                    ->withErrors(['user_id' => 'User not found.']);
            }
        } else {
            // Handle case where the UserOrganization relationship is not found
            return redirect()->route('admin.select.organization')
                ->withErrors(['organization_id' => 'Invalid selection or organization.']);
        }
    }    
        
    public function adminLogout()
    {
        Auth::logout();
        return redirect('/')->with('success', 'Logged out successfully.');
    }
}