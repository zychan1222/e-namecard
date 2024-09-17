<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterEmailRequest;
use App\Http\Requests\RegisterOrganizationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\RegisterService;

class RegisterController extends Controller
{
    protected $registerService;

    public function __construct(RegisterService $registerService)
    {
        $this->registerService = $registerService;
    }

    public function showEmailForm()
    {
        return view('auth.email-registration');
    }

    public function registerEmail(RegisterEmailRequest $request)
    {
        $email = $request->input('email');
        $name = $request->input('name', 'Default Name');

        $this->registerService->registerEmail($email, $name);

        return redirect()->route('verify.tac.form', ['email' => $email])->with('success', 'TAC sent to your email.');
    }

    public function showTACForm($email)
    {
        return view('auth.verify-tac', ['email' => $email]);
    }

    public function verifyTAC(Request $request, $email)
    {
        $tac = $request->input('tac');

        if ($this->registerService->verifyTAC($email, $tac)) {
            $user = $this->registerService->getUserRepository()->findByEmail($email);
            Auth::login($user);

            return redirect()->route('register.organization')->with('success', 'TAC verified successfully. Proceed to organization registration.');
        }

        return redirect()->route('verify.tac.form', ['email' => $email])->withErrors(['tac' => 'Invalid TAC or TAC expired.']);
    }

    public function showOrganizationForm()
    {
        return view('auth.organization-registration');
    }

    public function registerOrganization(RegisterOrganizationRequest $request)
    {
        $organizationData = $request->only(['name', 'email', 'address', 'phoneNo']);
        $user = Auth::user();

        $this->registerService->registerOrganization($organizationData, $user);

        return redirect()->route('organization.created')->with('success', 'Organization registered successfully!');
    }

    public function showOrganizationCreatedPage()
    {
        return view('organization-created')->with('success', session('success'));
    }
}
