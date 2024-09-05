<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\StoreEmployeeRequest;
use App\Services\EmployeeProfileService;
use Illuminate\Support\Facades\Log;

class EmployeeProfileController extends Controller
{
    protected $employeeProfileService;

    public function __construct(EmployeeProfileService $employeeProfileService)
    {
        $this->employeeProfileService = $employeeProfileService;
    }

    public function viewEmployeeProfile($employeeId)
    {
        $adminId = session('admin_id');

        if (!$adminId) {
            return redirect()->route('admin.login.form')->withErrors(['error' => 'Unauthorized access. Please log in as an admin.']);
        }

        try {
            $data = $this->employeeProfileService->getEmployeeProfileData($adminId, $employeeId);
            return view('admin.employee-profile', $data);
        } catch (\Exception $e) {
            Log::error('Error fetching employee profile', ['employee_id' => $employeeId, 'error' => $e->getMessage()]);
            return redirect()->back()->withErrors(['error' => 'Failed to retrieve employee profile.']);
        }
    }

    public function update(UpdateProfileRequest $request, $employeeId)
    {
        try {
            $data = $request->validated();
            $this->employeeProfileService->updateEmployeeProfile($data, $employeeId, $request->file('profile_pic'));
            return redirect()->route('admin.employee.profile', ['employee' => $employeeId])
                             ->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating employee profile', ['employee_id' => $employeeId, 'error' => $e->getMessage()]);
            return redirect()->back()->withErrors(['error' => 'Failed to update profile. Please try again.']);
        }
    }    

    public function create()
    {
        $adminId = session('admin_id');

        if (!$adminId) {
            return redirect()->route('admin.login.form')->withErrors(['error' => 'Unauthorized access. Please log in as an admin.']);
        }

        try {
            $data = $this->employeeProfileService->getCreateEmployeeData($adminId);
            return view('admin.create-employee', $data);
        } catch (\Exception $e) {
            Log::error('Error fetching data for creating employee', ['error' => $e->getMessage()]);
            return redirect()->back()->withErrors(['error' => 'Failed to retrieve data for creating employee.']);
        }
    }

    public function store(StoreEmployeeRequest $request)
    {
        try {
            $data = $request->validated();
            $adminId = session('admin_id');
            $this->employeeProfileService->storeEmployee($data, $adminId);
            return redirect()->route('admin.dashboard')->with('success', 'Employee created successfully!');
        } catch (\Exception $e) {
            Log::error('Error creating employee', ['error' => $e->getMessage(), 'data' => $request->all()]);
            return redirect()->back()->withErrors(['error' => 'Failed to create employee. Please try again.']);
        }
    }

    public function destroy($employeeId)
    {
        $adminId = session('admin_id');

        if (!$adminId) {
            return redirect()->route('admin.login.form')->withErrors(['error' => 'Unauthorized access. Please log in as an admin.']);
        }

        try {
            $this->employeeProfileService->destroyEmployee($adminId, $employeeId);
            return redirect()->route('admin.dashboard')->with('success', 'Employee deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting employee', ['employee_id' => $employeeId, 'error' => $e->getMessage()]);
            return redirect()->back()->withErrors(['error' => 'Failed to delete employee.']);
        }
    }
}
