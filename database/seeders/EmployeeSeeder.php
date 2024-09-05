<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\User;
use App\Models\Organization;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        // Ensure the organization with ID 10 exists
        $organizationId = 10;
        if (!Organization::find($organizationId)) {
            // Optionally, create the organization if it doesn't exist
            $organization = Organization::factory()->create(['id' => $organizationId]);
        }

        // Create employees for the organization with ID 10
        Employee::factory()
            ->count(10) // Specify how many employees you want to create
            ->create([
                'company_id' => $organizationId, // Set the company_id to 10
            ]);
    }
}
