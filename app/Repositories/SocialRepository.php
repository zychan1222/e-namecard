<?php
namespace App\Repositories;

use App\Models\Employee;
use App\Models\SocialConnection;

class SocialRepository
{
    public function findEmployeeByEmail($email)
    {
        return Employee::where('email', $email)->first();
    }

    public function createEmployee(array $employeeData)
    {
        return Employee::create($employeeData);
    }

    public function createSocialConnection($employee, $socialUser, $provider)
    {
        SocialConnection::create([
            'employee_id' => $employee->id,
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
            'access_token' => $socialUser->token,
        ]);
    }
}
