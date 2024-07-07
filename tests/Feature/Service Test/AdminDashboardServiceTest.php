<?php

use App\Services\AdminDashboardService;
use App\Repositories\AdminRepository;
use App\Repositories\UserRepository;
use App\Models\Admin;
use App\Models\Employee;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;


uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->adminRepository = new AdminRepository();
    $this->userRepository = new UserRepository();

    $this->adminDashboardService = new AdminDashboardService(
        $this->adminRepository,
        $this->userRepository
    );
});

test('get authenticated admin', function () {
    $admin = Admin::factory()->create();
    $this->be($admin, 'admin');

    // Log in as admin
    $authenticatedAdmin = $this->adminDashboardService->getAuthenticatedAdmin();

    expect($authenticatedAdmin)->toBeInstanceOf(Admin::class);
    expect($authenticatedAdmin->id)->toEqual($admin->id);
});

test('get employee from admin', function () {
    $employee = Employee::factory()->create();
    $admin = Admin::factory()->create(['employee_id' => $employee->id]);

    $foundEmployee = $this->adminDashboardService->getEmployeeFromAdmin($admin);

    expect($foundEmployee)->toBeInstanceOf(Employee::class);
    expect($foundEmployee->id)->toEqual($employee->id);
});

test('get all employees paginated', function () {
    // Clear any existing data to start fresh
    Employee::query()->delete();

    // Create exactly 30 employees
    Employee::factory()->count(30)->create();

    // Retrieve paginated employees
    $paginatedEmployees = $this->adminDashboardService->getAllEmployeesPaginated(10);

    // Perform assertions
    expect($paginatedEmployees)->toBeInstanceOf(LengthAwarePaginator::class);
    expect($paginatedEmployees->perPage())->toEqual(10);
    expect($paginatedEmployees->total())->toEqual(30);
});
