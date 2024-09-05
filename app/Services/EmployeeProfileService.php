<?php

namespace App\Services;

use App\Mail\AccountCreatedNotification;
use App\Repositories\EmployeeProfileRepository;
use Illuminate\Support\Facades\Mail;

class EmployeeProfileService
{
    protected $employeeProfileRepository;

    public function __construct(EmployeeProfileRepository $employeeProfileRepository)
    {
        $this->employeeProfileRepository = $employeeProfileRepository;
    }

    public function getEmployeeProfileData($adminId, $employeeId)
    {
        $admin = $this->employeeProfileRepository->findAdminById($adminId);
        $employee = $this->employeeProfileRepository->findEmployeeById($employeeId);

        if (!$admin || !$employee) {
            throw new \Exception('Admin or employee not found.');
        }

        $adminEmployee = $this->employeeProfileRepository->findEmployeeById($admin->employee_id);
        $pageTitle = 'Employee Profile Page';
        $editMode = false;

        return compact('employee', 'adminEmployee', 'pageTitle', 'editMode');
    }

    public function updateEmployeeProfile($data, $employeeId, $profilePic = null)
    {
        $employee = $this->employeeProfileRepository->findEmployeeById($employeeId);

        if ($profilePic) {
            $profilePicPath = $profilePic->move(public_path('storage/profile_pics'), $profilePic->getClientOriginalName());

            if ($employee->profile_pic && file_exists(public_path('storage/profile_pics/' . $employee->profile_pic))) {
                unlink(public_path('storage/profile_pics/' . $employee->profile_pic));
            }

            $data['profile_pic'] = $profilePic->getClientOriginalName();
        }

        $this->employeeProfileRepository->updateEmployee($employee, $data);
    }

    public function getCreateEmployeeData($adminId)
    {
        $admin = $this->employeeProfileRepository->findAdminById($adminId);

        if (!$admin) {
            throw new \Exception('Admin not found.');
        }

        $adminEmployee = $this->employeeProfileRepository->findEmployeeById($admin->employee_id);
        $organization = $adminEmployee->organization;

        $pageTitle = 'Create Employee Profile';

        return compact('pageTitle', 'organization', 'adminEmployee');
    }

    public function storeEmployee($data, $adminId)
    {
        $user = $this->employeeProfileRepository->findUserByEmail($data['email']);

        if (!$user) {
            $user = $this->employeeProfileRepository->createUser(['email' => $data['email']]);
        }

        $admin = $this->employeeProfileRepository->findAdminById($adminId);
        $adminEmployee = $this->employeeProfileRepository->findEmployeeById($admin->employee_id);
        $data['user_id'] = $user->id;
        $data['company_id'] = $adminEmployee->company_id;

        $employee = $this->employeeProfileRepository->createEmployee($data);

        Mail::to($data['email'])->send(new AccountCreatedNotification($employee));
    }

    public function destroyEmployee($adminId, $employeeId)
    {
        $admin = $this->employeeProfileRepository->findAdminById($adminId);

        if ($admin->employee_id == $employeeId) {
            throw new \Exception('You cannot delete your own profile.');
        }

        $adminEntry = $this->employeeProfileRepository->findAdminByEmployeeId($employeeId);

        if ($adminEntry) {
            $this->employeeProfileRepository->deleteAdmin($adminEntry);
        }

        $this->employeeProfileRepository->deleteEmployee($employeeId);
    }
}
