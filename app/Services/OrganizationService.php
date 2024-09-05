<?php

namespace App\Services;

use App\Repositories\OrganizationRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\UserRepository;
use App\Models\Admin;
use Illuminate\Support\Facades\Log;

class OrganizationService
{
    protected $organizationRepository;
    protected $employeeRepository;
    protected $userRepository;

    public function __construct(OrganizationRepository $organizationRepository, EmployeeRepository $employeeRepository,UserRepository $userRepository) 
    {
        $this->organizationRepository = $organizationRepository;
        $this->employeeRepository = $employeeRepository;
        $this->userRepository = $userRepository;
    }

    public function registerOrganization(array $organizationData, $userId)
    {
        $organization = $this->organizationRepository->create($organizationData);
        $user = $this->userRepository->findById($userId);

        Log::info('Registering organization', ['user_id' => $userId, 'organization_data' => $organizationData]);

        if ($user) {
            Log::info('User found', ['user' => $user]);

            $employee = $this->employeeRepository->create([
                'user_id' => $user->id,
                'company_id' => $organization->id,
                'is_active' => true,
            ]);

            Log::info('Employee created', ['employee' => $employee]);

            $organization->owner_id = $employee->id;
            $this->organizationRepository->save($organization);

            Log::info('Organization updated', ['organization' => $organization]);

            Admin::create([
                'employee_id' => $employee->id,
                'role' => 'admin',
            ]);

            Log::info('Admin created');
        } else {
            Log::error('User not found', ['user_id' => $userId]);
        }

        return $organization;
    }

    public function registerAdmin(array $adminData)
    {
        $employee = $this->employeeRepository->create($adminData);
        $organization = $this->organizationRepository->findById($adminData['company_id']);
        $organization->owner_id = $employee->id;
        $this->organizationRepository->save($organization);

        return $employee;
    }

    public function updateOrganization($organizationId, array $data)
    {
        $organization = $this->organizationRepository->find($organizationId);

        // Handle logo upload
        if (isset($data['logo'])) {
            $logoPath = public_path('storage/logo');

            // Store the new logo in the public directory
            $logoFileName = $data['logo']->getClientOriginalName();
            $data['logo']->move($logoPath, $logoFileName);

            // Delete the old logo if it exists
            if ($organization->logo && file_exists(public_path('storage/logo/' . $organization->logo))) {
                unlink(public_path('storage/logo/' . $organization->logo));
            }

            // Store only the filename
            $data['logo'] = $logoFileName;
        }

        return $this->organizationRepository->update($organization, $data);
    }
}
