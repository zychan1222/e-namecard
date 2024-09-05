<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function showDashboard()
    {
        $employeeId = session('employee_id');

        $employee = $this->dashboardService->getAuthenticatedEmployee($employeeId);
        
        if (!$employee) {
            return $this->handleInvalidEmployee();
        }

        return $this->showDashboardPage($employee, $employeeId);
    }

    protected function isAuthenticated()
    {
        return Auth::check();
    }

    protected function handleInvalidEmployee()
    {
        session()->forget('employee_id');
        return redirect()->route('login');
    }

    protected function showDashboardPage($employee, $employeeId)
    {
        $pageTitle = 'Dashboard';
        return view('dashboard', compact('employee', 'employeeId', 'pageTitle'));
    }
}