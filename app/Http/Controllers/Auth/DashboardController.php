<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;

class DashboardController extends Controller
{
    public function showDashboard()
    {
        if (Auth::check()) {
            $employee = $this->getAuthenticatedEmployee();
            $pageTitle = 'Dashboard'; 
            return $this->showDashboardPage($employee, $pageTitle);
        } else {
            return redirect()->route('login');
        }
    }

    protected function getAuthenticatedEmployee()
    {
        return Auth::user();
    }

    protected function showDashboardPage(Employee $employee, $pageTitle)
    {
        return view('dashboard', compact('employee', 'pageTitle'));
    }
}
