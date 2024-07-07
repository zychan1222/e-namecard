<?php

namespace App\Services;

use App\Repositories\AdminRepository;
use App\Repositories\UserRepository;
use App\Models\Admin;
use App\Models\Employee;

class AdminDashboardService
{
    protected $adminRepository;
    protected $userRepository;

    public function __construct(AdminRepository $adminRepository, UserRepository $userRepository)
    {
        $this->adminRepository = $adminRepository;
        $this->userRepository = $userRepository;
    }

    public function getAuthenticatedAdmin()
    {
        return auth()->guard('admin')->user();
    }

    public function getEmployeeFromAdmin(Admin $admin): ?Employee
    {
        return $this->userRepository->findById($admin->employee_id);
    }

    public function getAllEmployeesPaginated(int $perPage = 10)
    {
        return Employee::paginate($perPage);
    }
}
?>