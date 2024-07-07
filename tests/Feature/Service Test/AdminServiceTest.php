<?php

use App\Services\AdminService;
use App\Repositories\AdminRepository;
use App\Repositories\UserRepository;
use App\Models\Admin;
use App\Models\Employee;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->adminRepository = new AdminRepository();
    $this->userRepository = new UserRepository();
    $this->adminService = new AdminService($this->adminRepository, $this->userRepository);
});

test('successful admin login', function () {
    $password = 'password123';
    $employee = Employee::factory()->create([
        'password' => Hash::make($password),
    ]);
    $admin = Admin::factory()->create(['employee_id' => $employee->id]);

    $credentials = [
        'email' => $employee->email,
        'password' => $password,
    ];

    $authenticatedAdmin = $this->adminService->login($credentials);

    expect($authenticatedAdmin)->toBeInstanceOf(Admin::class);
    expect($authenticatedAdmin->id)->toEqual($admin->id);
    expect(Auth::guard('admin')->check())->toBeTrue();
});

test('admin login with invalid credentials', function () {
    $credentials = [
        'email' => 'nonexistent@example.com',
        'password' => 'invalidpassword',
    ];

    $this->expectException(ValidationException::class);
    $this->expectExceptionMessage('The provided credentials do not match our records.');

    $this->adminService->login($credentials);
});

test('admin login without admin access', function () {
    $password = 'password123';
    $employee = Employee::factory()->create([
        'password' => Hash::make($password),
    ]);

    $credentials = [
        'email' => $employee->email,
        'password' => $password,
    ];

    $this->expectException(ValidationException::class);
    $this->expectExceptionMessage('You do not have admin access.');

    $this->adminService->login($credentials);
});

test('admin logout', function () {
    $admin = Admin::factory()->create();
    Auth::guard('admin')->login($admin);

    $this->adminService->logout();

    expect(Auth::guard('admin')->check())->toBeFalse();
});
