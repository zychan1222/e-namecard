<?php

namespace App\Services;

use App\Repositories\AdminRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminService
{
    protected $adminRepository;
    protected $userRepository;

    public function __construct(AdminRepository $adminRepository, UserRepository $userRepository)
    {
        $this->adminRepository = $adminRepository;
        $this->userRepository = $userRepository;
    }

    public function login(array $credentials)
    {
        $employee = $this->userRepository->findByEmail($credentials['email']);

        if ($employee && Hash::check($credentials['password'], $employee->password)) {
            $admin = $this->adminRepository->getAdminByEmployeeId($employee->id);
            if ($admin) {
                Auth::guard('admin')->login($admin);
                return $admin;
            } else {
                throw ValidationException::withMessages([
                    'email' => 'You do not have admin access.',
                ]);
            }
        } else {
            throw ValidationException::withMessages([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
    }
}

?>
