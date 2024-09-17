<?php

namespace App\Repositories;

use App\Models\Organization;

class OrganizationRepository
{
    public function create(array $data)
    {
        return Organization::create($data);
    }
}
