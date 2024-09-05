<?php

namespace App\Services;

use App\Repositories\EmployeeRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\UploadedFile;

class ProfileService
{
    protected $employeeRepository;
    protected $userRepository;

    public function __construct(EmployeeRepository $employeeRepository, UserRepository $userRepository)
    {
        $this->employeeRepository = $employeeRepository;
        $this->userRepository = $userRepository;
    }

    public function getEmployeeById($employeeId)
    {
        return $this->employeeRepository->findById($employeeId);
    }

    public function getUserEmail($userId)
    {
        $user = $this->userRepository->findById($userId);
        return $user ? $user->email : 'Email not available';
    }

    public function updateProfile($employee, array $data)
    {
        // Handle profile picture upload
        if (isset($data['profile_pic']) && $data['profile_pic'] instanceof UploadedFile) {
            // Store the new profile picture in the public directory
            $profilePicPath = $data['profile_pic']->move(public_path('storage/profile_pics'), $data['profile_pic']->getClientOriginalName());

            // Delete the old profile picture if it exists
            if ($employee->profile_pic && file_exists(public_path('storage/profile_pics/' . $employee->profile_pic))) {
                unlink(public_path('storage/profile_pics/' . $employee->profile_pic));
            }

            // Store only the filename
            $data['profile_pic'] = $data['profile_pic']->getClientOriginalName();
        }

        // Update profile
        return $this->employeeRepository->update($employee, $data);
    }
}
