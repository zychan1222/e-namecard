<?php

namespace App\Repositories;

use App\Models\UserOrganization;

class UserOrganizationRepository
{
    public function getUserOrganizations($userIds)
    {
        return UserOrganization::whereIn('user_id', $userIds)
            ->with('organization')
            ->get();
    }

    public function findUserOrganization($userId, $organizationId)
    {
        return UserOrganization::where('user_id', $userId)
            ->where('organization_id', $organizationId)
            ->first();
    }
}
