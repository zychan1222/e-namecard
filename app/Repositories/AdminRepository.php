<?php

namespace App\Repositories;

use App\Models\Employee;
use App\Models\Admin;

class AdminRepository
{

    public function getAdminByEmployeeId(int $employeeId)
    {
        return Admin::where('employee_id', $employeeId)->first();
    }
    
}
?>
