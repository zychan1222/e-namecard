<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DeleteUserPermissionSeeder extends Seeder
{
    public function run()
    {
        // Create the delete user permission
        $deleteUserPermission = Permission::create(['name' => 'delete users']);

        // Get the owner role
        $ownerRole = Role::where('name', 'owner')->first();

        // Assign the delete user permission to the owner role
        if ($ownerRole) {
            $ownerRole->givePermissionTo($deleteUserPermission);
        }
    }
}
