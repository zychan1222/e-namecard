<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use Illuminate\Support\Facades\Log;

class CheckEmployeeCompany
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $employeeId = $request->route('employee'); // Assuming 'employee' is a route parameter
        $employee = Employee::find($employeeId);

        if (!$employee) {
            abort(404, 'Employee not found');
        }

        // Retrieve admin and employee IDs from the session
        $adminId = $request->session()->get('admin_id');
        $adminEmployeeId = $request->session()->get('employee_id');

        // Log the session details for debugging
        Log::info('Session data', ['admin_id' => $adminId, 'employee_id' => $adminEmployeeId]);

        // If the admin ID or employee ID is not found in the session, return unauthorized
        if (!$adminId || !$adminEmployeeId) {
            abort(403, 'Unauthorized access');
        }

        // Find the admin employee using the employee_id from the session
        $adminEmployee = Employee::find($adminEmployeeId);

        // Check if the current admin employee belongs to the same company as the requested employee
        if ($adminEmployee->company_id !== $employee->company_id) {
            abort(403, 'Unauthorized access');
        }

        return $next($request);
    }
}
