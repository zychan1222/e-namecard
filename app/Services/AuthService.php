<?php
namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\AdminRepository;

class AuthService
{
    protected $userRepository;
    protected $employeeRepository;
    protected $adminRepository;
    protected $tacService;

    public function __construct(UserRepository $userRepository, EmployeeRepository $employeeRepository, AdminRepository $adminRepository, TACService $tacService)
    {
        $this->userRepository = $userRepository;
        $this->employeeRepository = $employeeRepository;
        $this->adminRepository = $adminRepository;
        $this->tacService = $tacService;
    }

    public function generateAndSendTAC($email)
    {
        $user = $this->userRepository->findByEmail($email);
        if ($user) {
            $tacCode = $this->tacService->generateTAC();
            $this->tacService->sendTAC($user, $tacCode);
            return $user;
        }
        return null;
    }

    public function getEmployeeEntries($userId)
    {
        return $this->employeeRepository->findByUserId($userId);
    }

    public function authenticateUser($email, $tacCode)
    {
        $user = $this->userRepository->findByEmail($email);
        if ($user && $user->tac_code === $tacCode && now()->lessThanOrEqualTo($user->tac_expiry)) {
            return $user;
        }
        return null;
    }

    public function findAdmin($employeeId)
    {
        return $this->adminRepository->findByEmployeeId($employeeId);
    }

    public function findEmployee($employeeId)
    {
        return $this->employeeRepository->findById($employeeId);
    }
}
