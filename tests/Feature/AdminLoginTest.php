<?php

use App\Models\Employee;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

test('admin can view login form', function () {
    $employee = Employee::factory()->create([
        'email' => 'employee@example.com',
        'password' => Hash::make('password123'),
    ]);

    $admin = Admin::factory()->create([
        'employee_id' => $employee->id,
    ]);

    $response = $this->get(route('admin.login'));

    $response->assertStatus(200);
    $response->assertViewIs('auth.admin-login');
});

test('admin can login with valid credentials', function () {
    $employee = Employee::factory()->create([
        'email' => 'employee2@example.com',
        'password' => Hash::make('password123'),
    ]);

    $admin = Admin::factory()->create([
        'employee_id' => $employee->id,
    ]);

    $response = $this->post(route('admin.login'), [
        'email' => 'employee2@example.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect('/admin/dashboard');
    $this->assertAuthenticated('admin');
});

test('admin cannot login with invalid password', function () {
    $employee = Employee::factory()->create([
        'email' => 'employee3@example.com',
        'password' => Hash::make('password123'),
    ]);

    $admin = Admin::factory()->create([
        'employee_id' => $employee->id,
    ]);

    $response = $this->post(route('admin.login'), [
        'email' => 'employee3@example.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertSessionHasErrors(['email']);
    $this->assertGuest('admin');
});

test('non-admin employee cannot login as admin', function () {
    $nonAdminEmployee = Employee::factory()->create([
        'email' => 'nonadmin@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->post(route('admin.login'), [
        'email' => 'nonadmin@example.com',
        'password' => 'password123',
    ]);

    $response->assertSessionHasErrors(['email']);
    $this->assertGuest('admin');
});

test('admin can logout', function () {
    $employee = Employee::factory()->create([
        'email' => 'employee4@example.com',
        'password' => Hash::make('password123'),
    ]);

    $admin = Admin::factory()->create([
        'employee_id' => $employee->id,
    ]);

    $this->actingAs($admin, 'admin');

    $response = $this->post(route('adminlogout'));

    $response->assertRedirect('/');
    $this->assertGuest('admin');
});
