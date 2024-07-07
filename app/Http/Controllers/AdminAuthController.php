<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AdminLoginRequest;
use App\Services\AdminService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    protected $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->middleware('guest:admin')->except('adminlogout');
        $this->adminService = $adminService;
    }

    public function showAdminLoginForm()
    {
        return view('auth.admin-login');
    }

    public function login(AdminLoginRequest $request)
    {
        $request->session()->flush();

        $credentials = $request->only('email', 'password');

        try {
            $this->adminService->login($credentials);
            return redirect()->intended('/admin/dashboard');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }
    }

    public function adminlogout(Request $request)
    {
        $request->session()->forget(Auth::guard('admin')->getName());
        $this->adminService->logout();
        return redirect('/');
    }
}
?>
