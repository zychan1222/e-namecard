<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Employee;
use App\Models\Organization;
use App\Repositories\AdminDashboardRepository;

class AdminDashboardService
{
    protected $adminDashboardRepository;

    public function __construct(AdminDashboardRepository $adminDashboardRepository)
    {
        $this->adminDashboardRepository = $adminDashboardRepository;
    }

    public function getAdminEmployeeDetails($adminId)
    {
        $admin = $this->adminDashboardRepository->findAdminById($adminId);
        if ($admin) {
            $employee = $this->adminDashboardRepository->findEmployeeById($admin->employee_id);
            return [$admin, $employee];
        }

        return [null, null];
    }

    public function fetchEmployees($companyId, $search = null, $filterAdmin = 'all', $sort = 'name_asc')
    {
        return $this->adminDashboardRepository->getEmployees($companyId, $search, $filterAdmin, $sort);
    }

    public function updateEmployeeRoles($roles, $companyId)
    {
        foreach ($roles as $employeeId => $role) {
            $employee = $this->adminDashboardRepository->findEmployeeInCompany($employeeId, $companyId);
            if ($employee) {
                $existingAdmin = $this->adminDashboardRepository->findAdminByEmployeeId($employeeId);

                if ($role === 'admin' && !$existingAdmin) {
                    Admin::create(['employee_id' => $employeeId, 'role' => 'admin']);
                } elseif ($role !== 'admin' && $existingAdmin) {
                    $existingAdmin->delete();
                }
            }
        }
    }

    public function getAllAdminsInCompany($companyId)
    {
        return $this->adminDashboardRepository->getAllAdminsInCompany($companyId);
    }

    public function getEmployeesInCompany($companyId)
    {
        return $this->adminDashboardRepository->getEmployeesInCompany($companyId);
    }

    public function getSearchMessage($search, $sort, $filterAdmin)
    {
        if (empty($search) && $filterAdmin === 'all') {
            return "Welcome! You can search for employees, filter by roles, and sort results.";
        }
    
        $searchMessage = !empty($search) ? "Searching for '{$search}'" : "No search term provided";
        $sortMessage = $this->getSortMessage($sort);
        $filterMessage = $this->getFilterMessage($filterAdmin);
    
        return $searchMessage . ", sorted by {$sortMessage}, and showing {$filterMessage}";
    }
    
    private function getSortMessage($sort)
    {
        switch ($sort) {
            case 'name_asc':
                return 'Name (A-Z)';
            case 'name_desc':
                return 'Name (Z-A)';
            case 'email_asc':
                return 'Email (A-Z)';
            case 'email_desc':
                return 'Email (Z-A)';
            default:
                return '';
        }
    }

    private function getFilterMessage($filterAdmin)
    {
        switch ($filterAdmin) {
            case 'admins_only':
                return 'Admins Only';
            case 'non_admins':
                return 'Non-Admins Only';
            case 'all':
            default:
                return 'All';
        }
    }

    public function clearSessionFilters()
    {
        session()->forget(['search', 'sortCriteria', 'filter_admin', 'search_message']);
        return ['success' => true, 'message' => 'Filters cleared successfully.'];
    }

    public function handleSearchAndSort($request, $employee)
    {
        $search = $request->input('search', session()->get('search', ''));
        $sort = $request->input('sort', session()->get('sortCriteria', 'name_asc'));
        $filterAdmin = $request->input('filter_admin', session()->get('filter_admin', 'all'));

        session([
            'search' => $search,
            'sortCriteria' => $sort,
            'filter_admin' => $filterAdmin,
        ]);

        return [
            'search' => $search,
            'sort' => $sort,
            'filterAdmin' => $filterAdmin,
        ];
    }
}
