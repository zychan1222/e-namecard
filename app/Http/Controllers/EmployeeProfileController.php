<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use App\Services\ProfileService;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Log;

class EmployeeProfileController extends Controller
{
    protected $userService;
    protected $profileService;

    public function __construct(UserService $userService, ProfileService $profileService)
    {
        $this->userService = $userService;
        $this->profileService = $profileService;
    }

    public function viewEmployeeProfile($id)
    {
        $employee = $this->userService->findById($id);
        $pageTitle = 'Employee Profile Page';
        $editMode = false;
        return view('admin.employee-profile', compact('employee', 'pageTitle', 'editMode'));
    }

    public function update(UpdateProfileRequest $request, $id)
    {
        $employee = $this->userService->findById($id);

        try {
            $data = $request->validated();

            Log::info('Validated data', ['data' => $data]);

            // Update logic
            $this->userService->updateProfile($employee, $data);

            Log::info('Employee profile updated successfully', ['employee_id' => $employee->id]);

            return redirect()->route('admin.employee.profile', ['id' => $id])
                             ->with('success', 'Profile updated successfully');
        } catch (\Exception $e) {
            Log::error('Error updating employee profile', ['employee_id' => $employee->id, 'error' => $e->getMessage()]);

            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}

