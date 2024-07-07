<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\Admin;

class AdminDashboardController extends Controller
{
    public function showAdminDashboard()
    {
        if (Auth::guard('admin')->check()) {
            $admin = $this->getAuthenticatedAdmin();
            $employee = $this->getEmployeeFromAdmin($admin);
            $pageTitle = 'admin.dashboard'; 
            return $this->showAdminDashboardPage($employee, $pageTitle);
        } else {
            return redirect()->route('admin.login');
        }
    }

    protected function getAuthenticatedAdmin()
    {
        return Auth::guard('admin')->user();
    }

    protected function getEmployeeFromAdmin(Admin $admin)
    {
        return Employee::find($admin->employee_id);
    }

    protected function showAdminDashboardPage(Employee $employee, $pageTitle)
    {
        $employees = Employee::all(); 
        $employees = Employee::paginate(10);
        return view('admin.dashboard', compact('employee', 'employees', 'pageTitle'));
    }
    
}
