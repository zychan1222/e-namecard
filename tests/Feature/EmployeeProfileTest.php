<?php

use App\Models\Admin;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\User;
use App\Services\EmployeeProfileService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->organization = Organization::factory()->create();
    $this->testUser = User::factory()->create();
    $this->testEmployee = Employee::factory()->create([
        'user_id' => $this->testUser->id,
        'company_id' => $this->organization->id
    ]);
    $this->adminUser = User::factory()->create();
    $this->adminEmployee = Employee::factory()->create([
        'user_id' => $this->adminUser->id,
        'company_id' => $this->organization->id
    ]);
    $this->admin = Admin::factory()->create([
        'employee_id' => $this->adminEmployee->id,
    ]);
    $this->actingAs($this->adminUser);
    $this->employeeProfileService = $this->createMock(EmployeeProfileService::class);
});

it('can view employee profile', function () {
    session(['admin_id' => $this->admin->id]);
    session(['employee_id' => $this->adminEmployee->id]);
    $this->employeeProfileService->method('getEmployeeProfileData')
        ->with($this->admin->id, $this->adminEmployee->id)
        ->willReturn(['employee' => $this->adminEmployee, 'pageTitle' => 'Employee Profile Page', 'editMode' => false]);
    app()->instance(EmployeeProfileService::class, $this->employeeProfileService);
    $response = $this->get(route('admin.employee.profile', ['employee' => $this->adminEmployee->id]));
    $response->assertStatus(200)
             ->assertViewIs('admin.employee-profile')
             ->assertSee($this->adminEmployee->name);
});

it('can update test user employee profile by admin', function () {
    session(['admin_id' => $this->admin->id]);
    session(['employee_id' => $this->adminEmployee->id]);
    $data = [
        'name' => 'Updated Test User Name',
        'name_cn' => 'Updated Name',
        'phone' => '1234567890',
        'department' => 'Updated Department',
        'designation' => 'Updated Designation',
        'is_active' => true,
    ];
    $this->employeeProfileService->expects($this->once())
        ->method('updateEmployeeProfile')
        ->with($data, $this->testEmployee->id, null);
    app()->instance(EmployeeProfileService::class, $this->employeeProfileService);
    $response = $this->put(route('admin.employee.update', ['employee' => $this->testEmployee->id]), $data);
    $response->assertRedirect(route('admin.employee.profile', ['employee' => $this->testEmployee->id]))
             ->assertSessionHas('success', 'Profile updated successfully!');
});

it('returns error when updating test user employee profile fails', function () {
    session(['admin_id' => $this->admin->id]);
    session(['employee_id' => $this->adminEmployee->id]);
    $data = [
        'name' => 'Updated Test User Name',
        'name_cn' => 'Updated Name',
        'phone' => '1234567890',
        'department' => 'Updated Department',
        'designation' => 'Updated Designation',
        'is_active' => true,
    ];
    $this->employeeProfileService->expects($this->once())
        ->method('updateEmployeeProfile')
        ->willThrowException(new \Exception('Update failed'));
    app()->instance(EmployeeProfileService::class, $this->employeeProfileService);
    $response = $this->put(route('admin.employee.update', ['employee' => $this->testEmployee->id]), $data);
    $response->assertRedirect()
             ->assertSessionHasErrors(['error' => 'Failed to update profile. Please try again.']);
});

it('can create a new employee profile', function () {
    session(['admin_id' => $this->admin->id]);
    session(['employee_id' => $this->adminEmployee->id]);
    $data = [
        'name' => 'New Employee',
        'name_cn' => 'New Employee',
        'email' => 'newemployee@example.com',
        'phone' => '0987654321',
        'department' => 'New Department',
        'designation' => 'New Designation',
        'is_active' => true,
    ];
    $this->employeeProfileService->expects($this->once())
        ->method('storeEmployee')
        ->with($data, $this->admin->id);
    app()->instance(EmployeeProfileService::class, $this->employeeProfileService);
    $response = $this->post(route('admin.employee.store'), $data);
    $response->assertRedirect(route('admin.dashboard'))
             ->assertSessionHas('success', 'Employee created successfully!');
});

it('returns error when creating a new employee profile fails', function () {
    session(['admin_id' => $this->admin->id]);
    session(['employee_id' => $this->adminEmployee->id]);
    $data = [
        'name' => 'New Employee',
        'name_cn' => 'New Employee',
        'email' => 'newemployee@example.com',
        'phone' => '0987654321',
        'department' => 'New Department',
        'designation' => 'New Designation',
        'is_active' => true,
    ];
    $this->employeeProfileService->expects($this->once())
        ->method('storeEmployee')
        ->willThrowException(new \Exception('Creation failed'));
    app()->instance(EmployeeProfileService::class, $this->employeeProfileService);
    $response = $this->post(route('admin.employee.store'), $data);
    $response->assertRedirect()
             ->assertSessionHasErrors(['error' => 'Failed to create employee. Please try again.']);
});

it('can delete admin employee profile', function () {
    session(['admin_id' => $this->admin->id]);
    session(['employee_id' => $this->adminEmployee->id]);
    $this->employeeProfileService->expects($this->once())
        ->method('destroyEmployee')
        ->with($this->admin->id, $this->adminEmployee->id);
    app()->instance(EmployeeProfileService::class, $this->employeeProfileService);
    $response = $this->delete(route('admin.employee.destroy', ['employee' => $this->adminEmployee->id]));
    $response->assertRedirect(route('admin.dashboard'))
             ->assertSessionHas('success', 'Employee deleted successfully.');
});

it('returns error when deleting employee profile fails', function () {
    session(['admin_id' => $this->admin->id]);
    session(['employee_id' => $this->adminEmployee->id]);
    $this->employeeProfileService->expects($this->once())
        ->method('destroyEmployee')
        ->willThrowException(new \Exception('Deletion failed'));
    app()->instance(EmployeeProfileService::class, $this->employeeProfileService);
    $response = $this->delete(route('admin.employee.destroy', ['employee' => $this->adminEmployee->id]));
    $response->assertRedirect()
             ->assertSessionHasErrors(['error' => 'Failed to delete employee.']);
});

it('returns error when admin is not in the same organization', function () {
    $differentOrganization = Organization::factory()->create();
    $differentAdminUser = User::factory()->create();
    $differentAdminEmployee = Employee::factory()->create([
        'user_id' => $differentAdminUser->id,
        'company_id' => $differentOrganization->id
    ]);
    $differentAdmin = Admin::factory()->create([
        'employee_id' => $differentAdminEmployee->id,
    ]);
    $this->actingAs($differentAdminUser);
    session(['admin_id' => $differentAdmin->id]);
    session(['employee_id' => $differentAdminEmployee->id]);
    $response = $this->get(route('admin.employee.profile', ['employee' => $this->adminEmployee->id]));
    $response->assertStatus(403)
             ->assertSee('Unauthorized access');
});