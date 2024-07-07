<?php

use App\Models\Admin;
use App\Models\Employee;


uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('guests are redirected to login', function () {
    $response = $this->get(route('admin.dashboard'));
    $response->assertRedirect(route('admin.login'));
});

test('authenticated admin can access dashboard', function () {
    $employee = Employee::factory()->create([
    ]);

    $admin = Admin::factory()->create([
        'employee_id' => $employee->id,
    ]);

    $this->actingAs($admin, 'admin');

    $response = $this->get(route('admin.dashboard'));
    $response->assertStatus(200);
    $response->assertSeeText('Admin Dashboard');
    $response->assertSeeText('Welcome back, ' . $employee->name);
});

test('employee list is displayed correctly', function () {
    $employee = Employee::factory()->create([
    ]);

    $admin = Admin::factory()->create([
        'employee_id' => $employee->id,
    ]);

    $this->actingAs($admin, 'admin');

    $response = $this->get(route('admin.dashboard'));
    $response->assertStatus(200);
    $response->assertSeeText('Employee List');

    $response->assertSeeText($employee->name);
    $response->assertSeeText($employee->email);
    $response->assertSee($employee->profile_pic ? asset('storage/' . $employee->profile_pic) : asset('storage/default-user.jpg'));
});

test('employee list pagination', function () {
    // Create an admin and associate with an employee
    $employee = Employee::factory()->create();
    $admin = Admin::factory()->create([
        'employee_id' => $employee->id,
    ]);

    // Create additional employees for pagination
    Employee::factory()->count(15)->create();

    // Acting as admin for authentication
    $this->actingAs($admin, 'admin');

    // Make a GET request to the admin dashboard route
    $response = $this->get(route('admin.dashboard'));

    // Assert that the response status is 200 (OK)
    $response->assertStatus(200);

    // Assert that the first page of employees is displayed
    $response->assertSeeInOrder(Employee::orderBy('id')->take(10)->pluck('name')->toArray());

    // Click on the "Next" link and assert the second page of employees
    $response = $this->get(route('admin.dashboard', ['page' => 2]));
    $response->assertSeeInOrder(Employee::orderBy('id')->skip(10)->take(10)->pluck('name')->toArray());

    // Click on the "Previous" link and assert back to the first page
    $response = $this->get(route('admin.dashboard', ['page' => 1]));
    $response->assertSeeInOrder(Employee::orderBy('id')->take(10)->pluck('name')->toArray());
});
