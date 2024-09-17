<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\UserOrganization;
use App\Models\TACCode;

class UserSeeder extends Seeder
{
    public function run()
    {
        $organizationId = 7;
        $roleId = 3;

        for ($i = 1; $i <= 20; $i++) {
            DB::transaction(function () use ($organizationId, $roleId, $i) {
                // Create a new user
                $user = User::create([
                    'name' => 'User ' . $i,
                    'name_cn' => '用户 ' . $i,
                    'email' => 'user' . $i . '@example.com',
                    'phone' => '1234567890',
                    'department' => 'Department ' . $i,
                    'designation' => 'Designation ' . $i,
                    'is_active' => true,
                    'password' => Hash::make('password'), // Use a default password or generate one
                ]);

                // Create an entry in TACCode table
                TACCode::create([
                    'email' => $user->email,
                    'tac_code' => null,
                    'expires_at' => null,
                ]);

                // Create an entry in UserOrganization table
                UserOrganization::create([
                    'user_id' => $user->id,
                    'organization_id' => $organizationId,
                    'role_id' => $roleId,
                ]);
            });
        }
    }
}
