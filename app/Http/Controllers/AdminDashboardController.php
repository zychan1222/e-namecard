<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Organization;
use App\Services\AdminDashboardService;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    protected $adminDashboardService;

    public function __construct(AdminDashboardService $adminDashboardService)
    {
        $this->adminDashboardService = $adminDashboardService;
    }

    public function showAdminDashboard(Request $request)
    {
        $adminId = $request->session()->get('admin_id');
        $employeeId = $request->session()->get('employee_id');

        if ($adminId) {
            list($admin, $employee) = $this->adminDashboardService->getAdminEmployeeDetails($adminId);

            if ($employee) {
                return $this->prepareDashboardView($employee, $admin, $adminId, $employeeId);
            }
        }

        return redirect()->route('admin.login.form');
    }    

    protected function prepareDashboardView($employee, $admin, $adminId, $employeeId)
    {
        $companyId = $employee->company_id;
        $employees = $this->adminDashboardService->fetchEmployees($companyId);
        $modalEmployees = Employee::where('company_id', $companyId)->get();
        $organization = Organization::find($companyId);
        $pageTitle = 'admin.dashboard';

        // Get search, sort, and filter values from the session
        $search = session()->get('search', '');
        $sort = session()->get('sortCriteria', 'name_asc');
        $filterAdmin = session()->get('filter_admin', 'all');

        // Generate the search message
        $searchMessage = $this->adminDashboardService->getSearchMessage($search, $sort, $filterAdmin);

        return view('admin.dashboard', compact('modalEmployees', 'employee', 'employees', 'pageTitle', 'organization', 'adminId', 'employeeId', 'admin', 'searchMessage'));
    }

    public function searchEmployees(Request $request)
    {
        $adminId = $request->session()->get('admin_id');
        $employeeId = $request->session()->get('employee_id');
    
        if (!$adminId) {
            return redirect()->route('admin.login.form');
        }
    
        if ($request->input('reset')) {
            return response()->json($this->adminDashboardService->clearSessionFilters());
        }
    
        list($admin, $employee) = $this->adminDashboardService->getAdminEmployeeDetails($adminId);
        $filters = $this->adminDashboardService->handleSearchAndSort($request, $employee);
        $searchMessage = $this->adminDashboardService->getSearchMessage($filters['search'], $filters['sort'], $filters['filterAdmin']);
        session(['search_message' => $searchMessage]);
    
        // Fetch employees for the modal
        $modalEmployees = Employee::where('company_id', $employee->company_id)->get();
        $employees = $this->adminDashboardService->fetchEmployees($employee->company_id, $filters['search'], $filters['filterAdmin'], $filters['sort']);
    
        if ($request->ajax()) {
            return view('partials.employee-list', compact('employees', 'modalEmployees'));
        }
    
        $employees->appends(['search' => $filters['search'], 'sort' => $filters['sort'], 'filter_admin' => $filters['filterAdmin']]);
        $pageTitle = 'admin.dashboard';
    
        return view('admin.dashboard', compact('employee', 'employees', 'modalEmployees', 'pageTitle', 'admin', 'searchMessage'));
    }    

    public function updateRoles(Request $request)
    {
        $roles = $request->input('roles', []);
        $adminId = $request->session()->get('admin_id');

        if ($adminId) {
            list($admin, $employee) = $this->adminDashboardService->getAdminEmployeeDetails($adminId);
            $this->adminDashboardService->updateEmployeeRoles($roles, $employee->company_id);

            return redirect()->back()->with('success', 'Employee roles updated successfully');
        }

        return redirect()->route('admin.login.form');
    }
}