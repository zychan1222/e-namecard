<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Models\Employee; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        $user = $this->userRepository->create($data);
        auth()->login($user);
        return $user;
    }

    public function login(array $credentials)
    {
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['These credentials do not match our records.'],
            ]);
        }

        $user = Auth::user();

        if ($user->is_active != 1) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => ['Your account is inactive.'],
            ]);
        }

        return $user;
    }

    public function updateProfile(Employee $employee, array $data)
    {
        Log::info('Updating employee profile', ['employee_id' => $employee->id, 'data' => $data]);

        // Use the repository to update the employee
        $updatedEmployee = $this->userRepository->update($employee, $data);

        Log::info('Employee profile updated', ['employee_id' => $updatedEmployee->id]);

        return $updatedEmployee;
    }

    public function findById($id): ?Employee
    {
        return $this->userRepository->findById($id);
    }
}