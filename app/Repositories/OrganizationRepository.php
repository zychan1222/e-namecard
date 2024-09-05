<?php
namespace App\Repositories;

use App\Models\Organization;

class OrganizationRepository
{
    public function create(array $data)
    {
        return Organization::create($data);
    }

    public function findById($id)
    {
        return Organization::find($id);
    }

    public function save(Organization $organization)
    {
        $organization->save();
    }

    public function find($organizationId)
    {
        return Organization::findOrFail($organizationId);
    }

    public function update($organization, array $data)
    {
        return $organization->update($data);
    }
    public function registerOrganization(array $data, $userId, $employeeId)
    {
        $organization = $this->create($data);
        $organization->owner_id = $employeeId;
        $organization->save();
        return $organization;
    }
}