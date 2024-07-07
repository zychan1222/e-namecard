<?php

use App\Repositories\AdminRepository;
use App\Models\Admin;
use App\Models\Employee;


uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->adminRepository = new AdminRepository();
});

test('get admin by employee id', function () {
    $employee = Employee::factory()->create();
    $admin = Admin::factory()->create([
        'employee_id' => $employee->id,
    ]);

    // Fetch the admin using the repository method
    $foundAdmin = $this->adminRepository->getAdminByEmployeeId($employee->id);

    // Assert that the admin is found and is the same as the created one
    expect($foundAdmin)->not->toBeNull();
    expect($foundAdmin)->toBeInstanceOf(Admin::class);
    expect($foundAdmin->id)->toEqual($admin->id);
    expect($foundAdmin->employee_id)->toEqual($employee->id);
});

test('get admin by employee id returns null for non existing employee', function () {
    // Fetch the admin using a non-existing employee ID
    $foundAdmin = $this->adminRepository->getAdminByEmployeeId(999);

    expect($foundAdmin)->toBeNull();
});
