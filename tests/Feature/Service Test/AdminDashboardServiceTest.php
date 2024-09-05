<?php

use App\Models\Admin;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\User;
use App\Repositories\AdminDashboardRepository;
use App\Services\AdminDashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->organization = Organization::factory()->create();
    $this->user = User::factory()->create();
    $this->employee = Employee::factory()->create([
        'user_id' => $this->user->id,
        'company_id' => $this->organization->id
    ]);
    $this->admin = Admin::factory()->create(['employee_id' => $this->employee->id]);
    $this->adminDashboardRepository = Mockery::mock(AdminDashboardRepository::class);
    $this->adminDashboardService = new AdminDashboardService($this->adminDashboardRepository);
});

it('fetches admin and employee details', function () {
    $this->adminDashboardRepository
        ->shouldReceive('findAdminById')
        ->with($this->admin->id)
        ->andReturn($this->admin);

    $this->adminDashboardRepository
        ->shouldReceive('findEmployeeById')
        ->with($this->admin->employee_id)
        ->andReturn($this->employee);

    [$fetchedAdmin, $fetchedEmployee] = $this->adminDashboardService->getAdminEmployeeDetails($this->admin->id);

    expect($fetchedAdmin)->toBe($this->admin);
    expect($fetchedEmployee)->toBe($this->employee);
});

it('returns null for admin and employee if admin does not exist', function () {
    $this->adminDashboardRepository
        ->shouldReceive('findAdminById')
        ->with(999)
        ->andReturn(null);

    [$fetchedAdmin, $fetchedEmployee] = $this->adminDashboardService->getAdminEmployeeDetails(999);

    expect($fetchedAdmin)->toBeNull();
    expect($fetchedEmployee)->toBeNull();
});

it('fetches employees based on search and filter criteria', function () {
    $employees = Employee::factory()->count(3)->create(['company_id' => $this->organization->id]);

    $this->adminDashboardRepository
        ->shouldReceive('getEmployees')
        ->with($this->organization->id, 'John', 'all', 'name_asc')
        ->andReturn($employees);

    $fetchedEmployees = $this->adminDashboardService->fetchEmployees($this->organization->id, 'John', 'all', 'name_asc');

    expect($fetchedEmployees)->toHaveCount(3);
});

it('creates an admin when updating employee roles to admin', function () {
    $this->adminDashboardRepository
        ->shouldReceive('findEmployeeInCompany')
        ->with($this->employee->id, $this->organization->id)
        ->andReturn($this->employee);

    $this->adminDashboardRepository
        ->shouldReceive('findAdminByEmployeeId')
        ->with($this->employee->id)
        ->andReturn(null);

    $this->adminDashboardService->updateEmployeeRoles([$this->employee->id => 'admin'], $this->organization->id);

    $this->assertDatabaseHas('admins', [
        'employee_id' => $this->employee->id,
        'role' => 'admin',
    ]);
});

it('deletes an admin when the role is not admin', function () {
    $this->adminDashboardRepository
        ->shouldReceive('findEmployeeInCompany')
        ->with($this->employee->id, $this->organization->id)
        ->andReturn($this->employee);

    $this->adminDashboardRepository
        ->shouldReceive('findAdminByEmployeeId')
        ->with($this->employee->id)
        ->andReturn($this->admin);

    $this->adminDashboardService->updateEmployeeRoles([$this->employee->id => 'user'], $this->organization->id);

    expect(Admin::where('employee_id', $this->employee->id)->exists())->toBeFalse();
});

it('fetches all admins in the company', function () {
    $admins = Admin::factory()->count(2)->create(['employee_id' => Employee::factory()->create(['company_id' => $this->organization->id])->id]);

    $this->adminDashboardRepository
        ->shouldReceive('getAllAdminsInCompany')
        ->with($this->organization->id)
        ->andReturn($admins);

    $fetchedAdmins = $this->adminDashboardService->getAllAdminsInCompany($this->organization->id);

    expect($fetchedAdmins)->toHaveCount(2);
    expect($fetchedAdmins)->toContain($admins[0]);
    expect($fetchedAdmins)->toContain($admins[1]);
});

it('fetches employees in the company', function () {
    $employees = Employee::factory()->count(3)->create(['company_id' => $this->organization->id]);

    $this->adminDashboardRepository
        ->shouldReceive('getEmployeesInCompany')
        ->with($this->organization->id)
        ->andReturn($employees);

    $fetchedEmployees = $this->adminDashboardService->getEmployeesInCompany($this->organization->id);

    expect($fetchedEmployees)->toHaveCount(3);
});
