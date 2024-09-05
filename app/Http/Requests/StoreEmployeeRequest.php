<?php

namespace App\Http\Requests;

use App\Repositories\EmployeeProfileRepository;
use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    protected $employeeProfileRepository;

    public function __construct(EmployeeProfileRepository $employeeProfileRepository)
    {
        $this->employeeProfileRepository = $employeeProfileRepository;
    }

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $adminId = session('admin_id');
        $admin = $this->employeeProfileRepository->findAdminById($adminId);
    
        if (!$admin) {
            throw new \Exception('Admin not found.');
        }
    
        $adminEmployee = $this->employeeProfileRepository->findEmployeeById($admin->employee_id);
    
        if (!$adminEmployee) {
            throw new \Exception('Admin employee not found.');
        }
    
        $companyId = $adminEmployee->company_id;
    
        return [
            'name' => ['required', 'string', 'max:255'],
            'name_cn' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:15'],
            'email' => [
                'required',
                'email',
                'max:255',
                function ($attribute, $value, $fail) use ($companyId) {
                    $existingEmployee = $this->employeeProfileRepository->findEmployeeByUserIdAndCompanyId($value, $companyId);
                    if ($existingEmployee) {
                        $fail('The email has already been used for an employee in this organization.');
                    }
                },
            ],
            'department' => ['required', 'string', 'max:255'],
            'designation' => ['required', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
        ];
    }       
}    