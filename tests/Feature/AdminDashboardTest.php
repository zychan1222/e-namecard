<?php

use App\Models\Admin;
use App\Models\Employee;
use App\Models\User;
use App\Models\Organization;
use App\Services\AdminDashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;

uses(RefreshDatabase::class)->group('admin-dashboard');

beforeEach(function () {
    $this->organization = Organization::factory()->create();
    $this->user = User::factory()->create();
    $this->employee = Employee::factory()->create([
        'user_id' => $this->user->id,
        'company_id' => $this->organization->id,
    ]);
    $this->admin = Admin::factory()->create(['employee_id' => $this->employee->id]);

    $this->adminDashboardServiceMock = $this->createMock(AdminDashboardService::class);
    $this->app->instance(AdminDashboardService::class, $this->adminDashboardServiceMock);

    $this->assertNotNull($this->employee); // Ensure the employee is created
});

it('shows the admin dashboard', function () {
    $this->withSession([
        'admin_id' => $this->admin->id,
        'employee_id' => $this->employee->id,
    ]);

    $this->adminDashboardServiceMock
        ->method('getAdminEmployeeDetails')
        ->with($this->admin->id)
        ->willReturn([$this->admin, $this->employee]);

    $employees = collect([$this->employee]);
    $paginatedEmployees = new LengthAwarePaginator(
        $employees->forPage(1, 10),
        $employees->count(),
        10,
        1,
        ['path' => url()->current()]
    );

    $this->adminDashboardServiceMock
        ->method('fetchEmployees')
        ->with($this->employee->company_id, null, 'all', 'name_asc')
        ->willReturn($paginatedEmployees);

    $searchMessage = "Welcome! You can search for employees, filter by roles, and sort results.";
    $this->adminDashboardServiceMock
        ->method('getSearchMessage')
        ->willReturn($searchMessage);

    $response = $this->get(route('admin.dashboard'));

    $response->assertStatus(200)
        ->assertViewIs('admin.dashboard')
        ->assertSee($this->employee->name)
        ->assertViewHas('searchMessage', $searchMessage)
        ->assertViewHas('employees', $paginatedEmployees);
});

it('redirects to login form if admin not authenticated', function () {
    $response = $this->get(route('admin.dashboard'));
    $response->assertRedirect(route('admin.login.form'));
});

it('redirects to login form if searching without being authenticated', function () {
    $response = $this->get(route('admin.dashboard.search'));
    $response->assertRedirect(route('admin.login.form'));
});

it('updates roles successfully', function () {
    $this->withSession(['admin_id' => $this->admin->id, 'employee_id' => $this->employee->id]);

    $this->adminDashboardServiceMock
        ->method('getAdminEmployeeDetails')
        ->with($this->admin->id)
        ->willReturn([$this->admin, $this->employee]);

    $roles = [$this->employee->id => 'admin'];
    $this->adminDashboardServiceMock
        ->expects($this->once())
        ->method('updateEmployeeRoles')
        ->with($roles, $this->employee->company_id);

    $response = $this->post(route('admin.update-roles'), ['roles' => $roles]);

    $response->assertRedirect()->with('success', 'Employee roles updated successfully');
});

it('redirects to login form if updating roles without being authenticated', function () {
    $response = $this->post(route('admin.update-roles'));
    $response->assertRedirect(route('admin.login.form'));
});
