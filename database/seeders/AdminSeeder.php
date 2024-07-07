<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $employee = Employee::create([
            'name' => 'John Doe',
            'name_cn' => '约翰·多伊',
            'designation' => 'Manager',
            'phone' => '1234567890',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'), 
            'profile_pic' => 'path/to/profile_pic.jpg',
            'department' => 'IT',
            'company_name' => 'Your Company',
            'is_active' => true,
        ]);

        Admin::create([
            'employee_id' => $employee->id,
            'role' => 'superadmin',
        ]);
    }
}
