<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterEmailRequest;
use App\Http\Requests\RegisterOrganizationRequest;
use App\Http\Requests\RegisterAdminRequest;
use App\Services\UserService;
use App\Services\EmployeeService;
use App\Services\OrganizationService;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    protected $userService;
    protected $employeeService;
    protected $organizationService;

    public function __construct(UserService $userService, EmployeeService $employeeService, OrganizationService $organizationService) 
    {
        $this->userService = $userService;
        $this->employeeService = $employeeService;
        $this->organizationService = $organizationService;
    }

    public function showEmailForm()
    {
        return view('auth.email-registration');
    }

    public function registerEmail(RegisterEmailRequest $request)
    {
        $email = $request->input('email');
        $user = $this->userService->registerEmail($email);

        session(['user_id' => $user->id]);

        return redirect()->route('verify.tac.form', ['email' => $email])->with('success', 'TAC sent to your email.'); // Success message
    }

    public function verifyTAC(Request $request, $email)
    {
        $tac = $request->input('tac');
    
        if ($this->userService->verifyTAC($email, $tac)) {
            $userId = session('user_id');

            return redirect()->route('register.organization')->with('success', 'TAC verified successfully. Proceed to organization registration.'); // Success message
        }
    
        return redirect()->route('verify.tac.form', ['email' => $email])->withErrors(['tac' => 'Invalid TAC.']);
    }

    public function showOrganizationForm()
    {
        return view('auth.organization-registration');
    }

    public function showTACForm($email)
    {
        return view('auth.verify-tac', ['email' => $email]);
    }

    public function registerOrganization(RegisterOrganizationRequest $request)
    {
        $organizationData = $request->all();
        $userId = session('user_id');
        $employeeId = session('employee_id');

        $organization = $this->organizationService->registerOrganization($organizationData, $userId, $employeeId);

        $this->employeeService->updateEmployeeCompany($employeeId, $organization->id);

        session()->forget('user_id');
        session()->forget('employee_id');

        return redirect()->route('organization.created')->with('success', 'Organization registered successfully!'); // Success message
    }    

    public function showAdminRegistrationForm()
    {
        return redirect()->route('register.email.form');
    }

    public function registerAdmin(RegisterAdminRequest $request)
    {
        $adminData = $request->all();
        $adminData['company_id'] = session('organization_id');
        $this->organizationService->registerAdmin($adminData);

        session()->forget('organization_id');
        session()->flash('success', 'Admin registered successfully!');

        return redirect()->route('organization.created');
    }

    public function showOrganizationCreatedPage()
    {
        return view('organization-created')->with('success', session('success'));
    }
}
