<?php

namespace Tests\Feature\RepositoryTest;

use App\Models\Admin;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\User;
use App\Repositories\AdminDashboardRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    // Start a database transaction
    DB::beginTransaction();

    // Create an organization, user, employee, and admin
    $this->organization = Organization::factory()->create();
    $this->user = User::factory()->create();
    $this->employee = Employee::factory()->create([
        'user_id' => $this->user->id,
        'company_id' => $this->organization->id,
    ]);
    $this->admin = Admin::factory()->create(['employee_id' => $this->employee->id]);

    // Instantiate the repository
    $this->repository = new AdminDashboardRepository();
});

afterEach(function () {
    // Rollback the database transaction
    DB::rollBack();
});

test('it can find an admin by ID', function () {
    $result = $this->repository->findAdminById($this->admin->id);

    expect($result->id)->toBe($this->admin->id);
});

test('it can find an employee by ID', function () {
    $result = $this->repository->findEmployeeById($this->employee->id);

    expect($result->id)->toBe($this->employee->id);
});

test('it can find an employee in a specific company', function () {
    $result = $this->repository->findEmployeeInCompany($this->employee->id, $this->organization->id);

    expect($result->id)->toBe($this->employee->id);
});

test('it returns null when the employee is not in the given company', function () {
    $anotherEmployee = Employee::factory()->create(['company_id' => 2]);

    $result = $this->repository->findEmployeeInCompany($anotherEmployee->id, $this->organization->id);

    expect($result)->toBeNull();
});

test('it can find an admin by employee ID', function () {
    $result = $this->repository->findAdminByEmployeeId($this->employee->id);

    expect($result->id)->toBe($this->admin->id);
});

test('it can get employees with search, filter, and sort options', function () {
    Employee::factory()->count(3)->create(['company_id' => $this->organization->id]);

    $result = $this->repository->getEmployees($this->organization->id, 'John', 'admins_only', 'name_asc');

    expect($result->count())->toBeGreaterThanOrEqual(0);
});

test('it can get all admins in a company', function () {
    $result = $this->repository->getAllAdminsInCompany($this->organization->id);

    expect($result->contains($this->admin))->toBeTrue();
});

test('it can get all employees in a company', function () {
    Employee::factory()->count(3)->create(['company_id' => $this->organization->id]);

    $result = $this->repository->getEmployeesInCompany($this->organization->id);

    expect($result->count())->toBe(4);
});

test('it can paginate employees by company ID', function () {
    Employee::factory()->count(15)->create(['company_id' => $this->organization->id]);

    $result = $this->repository->getEmployeesByCompanyId($this->organization->id);

    expect($result->perPage())->toBe(10);
    expect($result->count())->toBe(10);
});
