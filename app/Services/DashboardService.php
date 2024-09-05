<?php

namespace App\Services;

use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardService
{
    public function getAuthenticatedEmployee($employeeId)
    {
        $userId = Auth::id();

        return Employee::where('user_id', $userId)
                       ->where('id', $employeeId)
                       ->first();
    }

    public function logDashboardAccess($employeeId)
    {
        $userId = Auth::id();

        Log::info('Dashboard access attempt.', [
            'user_id' => $userId,
            'employee_id' => $employeeId,
            'session_data' => session()->all()
        ]);
    }

    public function logEmployeeRetrieval($employee)
    {
        Log::info('Employee retrieval result.', [
            'employee_exists' => $employee ? true : false,
            'employee_data' => $employee ? $employee->toArray() : null
        ]);
    }
}
