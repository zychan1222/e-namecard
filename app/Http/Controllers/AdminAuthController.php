<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendTACRequest;
use App\Http\Requests\LoginWithTACRequest;
use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AdminAuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showAdminLoginForm()
    {
        return view('auth.admin-login');
    }

    public function sendTAC(SendTACRequest $request)
    {
        $email = $request->input('email');
        $user = $this->authService->generateAndSendTAC($email);
        
        if ($user) {
            session(['email' => $email]); 
            return redirect()->route('admin.login.tac.show')->with('success', 'TAC sent successfully to your email.');
        }

        return redirect()->route('admin.login.form')->withErrors(['email' => 'No user found with this email.']);
    }

    public function loginWithTAC(LoginWithTACRequest $request)
    {
        $email = $request->input('email');
        $tacCode = $request->input('tac_code');
        $user = $this->authService->authenticateUser($email, $tacCode);

        if ($user) {
            $this->storeUserInSession($user);
            return redirect()->route('admin.select.organization')->with('success', 'Logged in successfully.');
        }

        return redirect()->route('admin.login.tac.show')->withErrors(['tac_code' => 'Invalid TAC code or it has expired.']);
    }

    protected function storeUserInSession($user)
    {
        session(['user_id' => $user->id]);
        $employeeEntries = $this->authService->getEmployeeEntries($user->id);
        session(['employeeEntries' => $employeeEntries]);
    }

    public function showTACForm(Request $request)
    {
        return view('auth.admin-tac')->with('email', $request->session()->get('email'));
    }

    public function showOrganizationSelectionForm()
    {
        $employeeEntries = session('employeeEntries', []);
        return view('auth.admin-select_organization', compact('employeeEntries'));
    }

    public function selectOrganization(Request $request)
    {
        $employeeId = $request->input('employee_id');
        $employee = $this->findEmployeeOrFail($employeeId);
        
        $admin = $this->authService->findAdmin($employee->id);
        
        if ($admin) {
            $this->storeOrganizationInSession($employee, $admin);
            return redirect()->route('admin.dashboard')->with('success', 'Organization selected successfully.');
        }

        return redirect()->route('admin.select.organization')->withErrors(['employee_id' => 'You do not have admin access for this organization.']);
    }

    protected function findEmployeeOrFail($employeeId)
    {
        $employee = $this->authService->findEmployee($employeeId);
        
        if (!$employee) {
            return redirect()->route('admin.select.organization')->withErrors(['employee_id' => 'Invalid selection.']);
        }

        return $employee;
    }

    protected function storeOrganizationInSession($employee, $admin)
    {
        session(['company_id' => $employee->company_id]);
        session(['employee_id' => $employee->id]);
        session(['admin_id' => $admin->id]); 

        Auth::loginUsingId($employee->user_id);
        session(['current_employee' => $employee]);
    }

    public function adminLogout()
    {
        Auth::guard('admin')->logout();
        session()->flush();
        return redirect('/')->with('success', 'Logged out successfully.');
    }
}
