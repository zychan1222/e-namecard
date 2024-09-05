<?php

namespace App\Repositories;

use App\Models\Admin;
use App\Models\Employee;
use App\Models\User;

class EmployeeProfileRepository
{
    public function findAdminById($adminId)
    {
        return Admin::find($adminId);
    }

    public function findEmployeeById($employeeId)
    {
        return Employee::find($employeeId);
    }

    public function findUserByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    public function createUser($data)
    {
        return User::create($data);
    }

    public function createEmployee($data)
    {
        return Employee::create($data);
    }

    public function updateEmployee($employee, $data)
    {
        $employee->update($data);
    }

    public function findAdminByEmployeeId($employeeId)
    {
        return Admin::where('employee_id', $employeeId)->first();
    }

    public function deleteAdmin($admin)
    {
        $admin->delete();
    }

    public function deleteEmployee($employeeId)
    {
        $employee = $this->findEmployeeById($employeeId);
        $employee->delete();
    }

    public function findEmployeeByUserIdAndCompanyId($email, $companyId)
    {
        $user = User::where('email', $email)->first();
    
        if ($user) {
            return Employee::where('user_id', $user->id)
                ->where('company_id', $companyId)
                ->first();
        }
    
        return null;
    }
}
