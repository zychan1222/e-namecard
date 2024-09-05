<?php
namespace App\Repositories;

use App\Models\Admin;

class AdminRepository
{
    public function findByEmployeeId($employeeId)
    {
        return Admin::where('employee_id', $employeeId)->first();
    }
}