<?php

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use App\Models\Employee;
use App\Models\Admin;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Storage;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('admin can view employee profile page', function () {
    $employee = Employee::factory()->create();
    $admin = Admin::factory()->create(['employee_id' => $employee->id]);

    $this->actingAs($admin, 'admin');

    $response = $this->get(route('admin.employee.profile', ['id' => $employee->id]));

    $response->assertStatus(200)
             ->assertSee('Employee Profile Page')
             ->assertSee($employee->name);
});

test('admin updates name', function () {
    $employee = Employee::factory()->create();
    $admin = Admin::factory()->create(['employee_id' => $employee->id]);
    $data = ['name' => 'Updated Name'];

    $response = $this->actingAs($admin, 'admin')
                     ->put(route('admin.employee.update', ['id' => $employee->id]), $data);

    $response->assertRedirect()
             ->assertSessionHas('success', 'Profile updated successfully');

    $this->assertDatabaseHas('employees', ['id' => $employee->id, 'name' => 'Updated Name']);
});

test('admin updates Chinese name', function () {
    $employee = Employee::factory()->create();
    $admin = Admin::factory()->create(['employee_id' => $employee->id]);
    $data = ['name_cn' => 'Updated Chinese Name'];

    $response = $this->actingAs($admin, 'admin')
                     ->put(route('admin.employee.update', ['id' => $employee->id]), $data);

    $response->assertRedirect()
             ->assertSessionHas('success', 'Profile updated successfully');

    $this->assertDatabaseHas('employees', ['id' => $employee->id, 'name_cn' => 'Updated Chinese Name']);
});

test('admin updates phone number', function () {
    $employee = Employee::factory()->create();
    $admin = Admin::factory()->create(['employee_id' => $employee->id]);
    $data = ['phone' => '1234567890'];

    $response = $this->actingAs($admin, 'admin')
                     ->put(route('admin.employee.update', ['id' => $employee->id]), $data);

    $response->assertRedirect()
             ->assertSessionHas('success', 'Profile updated successfully');

    $this->assertDatabaseHas('employees', ['id' => $employee->id, 'phone' => '1234567890']);
});

test('admin updates company name', function () {
    $employee = Employee::factory()->create();
    $admin = Admin::factory()->create(['employee_id' => $employee->id]);
    $data = ['company_name' => 'New Company Name'];

    $response = $this->actingAs($admin, 'admin')
                     ->put(route('admin.employee.update', ['id' => $employee->id]), $data);

    $response->assertRedirect()
             ->assertSessionHas('success', 'Profile updated successfully');

    $this->assertDatabaseHas('employees', ['id' => $employee->id, 'company_name' => 'New Company Name']);
});

test('admin updates department', function () {
    $employee = Employee::factory()->create();
    $admin = Admin::factory()->create(['employee_id' => $employee->id]);
    $data = ['department' => 'New Department'];

    $response = $this->actingAs($admin, 'admin')
                     ->put(route('admin.employee.update', ['id' => $employee->id]), $data);

    $response->assertRedirect()
             ->assertSessionHas('success', 'Profile updated successfully');

    $this->assertDatabaseHas('employees', ['id' => $employee->id, 'department' => 'New Department']);
});

test('admin updates designation', function () {
    $employee = Employee::factory()->create();
    $admin = Admin::factory()->create(['employee_id' => $employee->id]);
    $data = ['designation' => 'New Designation'];

    $response = $this->actingAs($admin, 'admin')
                     ->put(route('admin.employee.update', ['id' => $employee->id]), $data);

    $response->assertRedirect()
             ->assertSessionHas('success', 'Profile updated successfully');

    $this->assertDatabaseHas('employees', ['id' => $employee->id, 'designation' => 'New Designation']);
});

test('admin updates is active status', function () {
    $employee = Employee::factory()->create();
    $admin = Admin::factory()->create(['employee_id' => $employee->id]);
    $data = ['is_active' => false];

    $response = $this->actingAs($admin, 'admin')
                     ->put(route('admin.employee.update', ['id' => $employee->id]), $data);

    $response->assertRedirect()
             ->assertSessionHas('success', 'Profile updated successfully');

    $this->assertDatabaseHas('employees', ['id' => $employee->id, 'is_active' => false]);
});

test('admin cannot update employee profile with invalid data', function () {
    $employee = Employee::factory()->create();
    $admin = Admin::factory()->create(['employee_id' => $employee->id]);

    $this->actingAs($admin, 'admin');

    $response = $this->put(route('admin.employee.update', ['id' => $employee->id]), [
        'name' => '',
        'email' => 'invalid_email',
    ]);

    $response->assertSessionHasErrors();
});

test('admin can cancel employee profile update', function () {
    $employee = Employee::factory()->create();
    $admin = Admin::factory()->create(['employee_id' => $employee->id]);

    $this->actingAs($admin, 'admin');

    $originalName = $employee->name;

    $response = $this->put(route('admin.employee.update', ['id' => $employee->id]), [
        'name' => $originalName,
        'email' => $employee->email,
    ]);

    $response = $this->get(route('admin.employee.profile', ['id' => $employee->id]));

    $response->assertStatus(200)
             ->assertSee($originalName);
});

test('admin cannot update employee profile with empty fields', function () {
    $employee = Employee::factory()->create();
    $admin = Admin::factory()->create(['employee_id' => $employee->id]);

    $this->actingAs($admin, 'admin');

    $response = $this->put(route('admin.employee.update', ['id' => $employee->id]), [
        'name' => '',
        'email' => '',
    ]);

    $response->assertSessionHasErrors();
});
