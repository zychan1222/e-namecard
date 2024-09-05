<?php
namespace App\Repositories;

use App\Models\Employee;

class EmployeeRepository
{
    public function create(array $data)
    {
        return Employee::create($data);
    }

    public function findById($id)
    {
        return Employee::find($id);
    }
    
    public function findByUserId($userId)
    {
        return Employee::where('user_id', $userId)->get(); 
    }

        public function update($employee, array $data)
    {
        return $employee->update($data);
    }

}