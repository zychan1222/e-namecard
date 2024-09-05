<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\SendTACRequest;
use App\Http\Requests\LoginWithTACRequest;
use App\Services\UserService;
use App\Services\EmployeeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    protected $userService;
    protected $employeeService;

    public function __construct(UserService $userService, EmployeeService $employeeService)
    {
        $this->userService = $userService;
        $this->employeeService = $employeeService;
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function sendTAC(SendTACRequest $request)
    {
        $email = $request->input('email');

        $user = $this->userService->generateAndSendTAC($email);
        
        if ($user) {
            session(['email' => $email]); 
            return redirect()->route('login.tac.show')->with('success', 'TAC sent successfully to your email.');
        }

        return redirect()->route('login')->withErrors(['email' => 'No user found with this email.']);
    }

    public function showTACForm()
    {
        return view('auth.tac');
    }

    public function loginWithTAC(LoginWithTACRequest $request)
    {
        $email = $request->input('email');
        $tacCode = $request->input('tac_code');
    
        if ($this->verifyTAC($email, $tacCode)) {
            return $this->handleSuccessfulLogin($email);
        }
    
        return redirect()->route('login.tac.show')->withErrors(['tac_code' => 'Invalid TAC code or it has expired.']);
    }
    
    protected function verifyTAC($email, $tacCode)
    {
        return $this->userService->verifyTAC($email, $tacCode);
    }
    
    protected function handleSuccessfulLogin($email)
    {
        $user = $this->userService->findByEmail($email);
        Auth::login($user);
    
        $employeeEntries = $this->employeeService->findByUserId($user->id);
        $this->storeEmployeeEntriesInSession($employeeEntries);
    
        return redirect()->route('select.organization')->with('success', 'Login successful.');
    }
    
    protected function storeEmployeeEntriesInSession($employeeEntries)
    {
        session(['employeeEntries' => $employeeEntries]);
    }       

    public function showOrganizationSelectionForm()
    {
        $employeeEntries = session('employeeEntries', []);
        return view('auth.select_organization', compact('employeeEntries'));
    }      

    public function selectOrganization(Request $request)
    {
        $employeeId = $request->input('employee_id');
    
        $employee = $this->employeeService->findById($employeeId);
        if ($employee && $employee->user_id == Auth::id()) {
            if ($employee->is_active == 0) {
                return redirect()->route('select.organization')->withErrors(['employee_id' => 'You account status is set as inactive.']);
            }
            
            session(['employee_id' => $employee->id]);
            session()->forget('employeeEntries');
    
            return redirect()->route('dashboard')->with('success', 'Organization selected successfully.');
        }
    
        return redirect()->route('select.organization')->withErrors(['employee_id' => 'Invalid selection.']);
    }

    public function logout()
    {
        Auth::logout();
        session()->flush();
        return redirect('/')->with('success', 'Successfully logged out.'); 
    }
}
