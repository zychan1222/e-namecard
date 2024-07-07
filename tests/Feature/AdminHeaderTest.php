<?php

use App\Models\Admin;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;


uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('dashboard link', function () {
    $employee = Employee::factory()->create();

    $admin = Admin::factory()->create([
        'employee_id' => $employee->id,
    ]);
    $response = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));

    $response->assertStatus(200);
});

test('unauthenticated user cannot view admin dashboard link', function () {
    $response = $this->get(route('admin.dashboard'));

    $response->assertRedirect(route('admin.login'));
});

test('profile picture retrieved', function () {
    $employee = Employee::factory()->create([
        'profile_pic' => 'profile_picture.jpg',
    ]);

    $admin = Admin::factory()->create([
        'employee_id' => $employee->id,
    ]);

    $response = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));

    $response->assertSee($employee->profile_pic);
});

test('default profile picture displayed when null', function () {
    $employee = Employee::factory()->create([
        'profile_pic' => null,
    ]);

    $admin = Admin::factory()->create([
        'employee_id' => $employee->id,
    ]);

    $response = $this->actingAs($admin, 'admin')->get(route('admin.dashboard'));

    $response->assertSee('default-user.jpg');
});

test('logout link', function () {
    $admin = Admin::factory()->create();

    $response = $this->actingAs($admin, 'admin')->post(route('adminlogout'));

    $response->assertRedirect('/');
    $this->assertGuest('admin');
});
