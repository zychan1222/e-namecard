<?php

namespace App\Repositories;

use App\Models\Admin;
use App\Models\Employee;
use App\Models\User;

class AdminDashboardRepository
{
    public function findAdminById($adminId)
    {
        return Admin::find($adminId);
    }

    public function findEmployeeById($employeeId)
    {
        return Employee::find($employeeId);
    }

    public function findEmployeeInCompany($employeeId, $companyId)
    {
        return Employee::where('company_id', $companyId)->find($employeeId);
    }

    public function findAdminByEmployeeId($employeeId)
    {
        return Admin::where('employee_id', $employeeId)->first();
    }

    public function getEmployees($companyId, $search = null, $filterAdmin = 'all', $sort = 'name_asc')
    {
        $query = Employee::with('user')->where('company_id', $companyId);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhereHas('user', fn($q) => $q->where('email', 'like', '%' . $search . '%'))
                    ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        if ($filterAdmin === 'admins_only') {
            $query->whereHas('admin');
        } elseif ($filterAdmin === 'non_admins') {
            $query->whereDoesntHave('admin');
        }

        switch ($sort) {
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'email_asc':
                $query->orderBy(User::select('email')->whereColumn('users.id', 'employees.user_id'), 'asc');
                break;
            case 'email_desc':
                $query->orderBy(User::select('email')->whereColumn('users.id', 'employees.user_id'), 'desc');
                break;
            default:
                $query->orderBy('name', 'asc');
        }

        return $query->paginate(10);
    }

    public function getAllAdminsInCompany($companyId)
    {
        return Admin::whereHas('employee', fn($query) => $query->where('company_id', $companyId))->get();
    }

    public function getEmployeesInCompany($companyId)
    {
        return Employee::with('user')->where('company_id', $companyId)->get();
    }

    public function getEmployeesByCompanyId($companyId)
    {
        return Employee::with('user')->where('company_id', $companyId)->paginate(10);
    }
    
}
