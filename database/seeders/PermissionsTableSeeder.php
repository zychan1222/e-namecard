<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        // Create the manage admins permission
        $manageAdminsPermission = Permission::create(['name' => 'manage roles']);

        // Get the owner role
        $ownerRole = Role::where('name', 'owner')->first();

        // Assign the manage admins permission to the owner role
        if ($ownerRole) {
            $ownerRole->givePermissionTo($manageAdminsPermission);
        }
    }
}