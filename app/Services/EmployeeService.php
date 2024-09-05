<?php

namespace App\Services;

use App\Repositories\EmployeeRepository;

class EmployeeService
{
    protected $employeeRepository;

    public function __construct(EmployeeRepository $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    public function findByUserId($userId)
    {
        return $this->employeeRepository->findByUserId($userId);
    }

    public function create(array $data)
    {
        return $this->employeeRepository->create($data);
    }

    public function findById($employeeId)
    {
        return $this->employeeRepository->findById($employeeId);
    }

    public function updateEmployeeCompany($employeeId, $companyId)
    {
        $employee = $this->employeeRepository->findById($employeeId);
    
        // Do not update if the employee already has a company associated
        if (!is_null($employee->company_id)) {
            return $employee; // No need to update if company_id is already set
        }
    
        // Otherwise, update the company_id
        return $this->employeeRepository->update($employee, ['company_id' => $companyId]);
    }    
}
