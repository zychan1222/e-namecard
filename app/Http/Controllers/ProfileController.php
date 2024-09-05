<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UpdateProfileRequest;
use App\Services\ProfileService;

class ProfileController extends Controller
{
    protected $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function view()
    {
        $employeeId = session('employee_id');
        $employee = $this->profileService->getEmployeeById($employeeId);
        
        if (!$employee) {
            return redirect()->route('login')->with('error', 'Employee not found.');
        }

        // Fetch the email from the users table
        $email = $this->profileService->getUserEmail($employee->user_id);

        $pageTitle = 'Profile Page';
        $editMode = false;
        return view('profile', compact('employee', 'pageTitle', 'editMode', 'email'));
    }
    
    public function edit()
    {
        $employeeId = session('employee_id');
        $employee = $this->profileService->getEmployeeById($employeeId);
        
        if (!$employee) {
            return redirect()->route('login')->with('error', 'Employee not found.');
        }

        // Fetch the email from the users table
        $email = $this->profileService->getUserEmail($employee->user_id);

        $pageTitle = 'Edit Profile';
        $editMode = true;
        return view('profile', compact('employee', 'pageTitle', 'editMode', 'email'));
    }

    public function update(UpdateProfileRequest $request)
    {
        $employeeId = session('employee_id');
        $employee = $this->profileService->getEmployeeById($employeeId);
    
        if (!$employee) {
            return redirect()->back()->with('error', 'Employee not found.');
        }
    
        try {
            $data = $request->validated();
            $this->profileService->updateProfile($employee, $data);
    
            return redirect()->back()->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while updating the profile.');
        }
    }    
}