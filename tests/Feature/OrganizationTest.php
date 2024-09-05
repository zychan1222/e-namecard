<?php

use App\Http\Controllers\OrganizationController;
use App\Models\Admin;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;

uses(RefreshDatabase::class)->group('admin-dashboard');

beforeEach(function () {
    $this->organization = Organization::factory()->create();
    $this->user = User::factory()->create();
    $this->employee = Employee::factory()->create(['user_id' => $this->user->id, 'company_id' => $this->organization->id]);
    $this->admin = Admin::factory()->create(['employee_id' => $this->employee->id]);
});

it('updates the organization successfully', function () {
    $updatedData = [
        'name' => 'Updated Organization Name',
        'address' => '123 Test St',
        'phoneNo' => '1234567890',
        'email' => 'test@example.com',
        'logo' => UploadedFile::fake()->image('logo.png'),
    ];

    $response = $this->put(route('admin.organization.update', $this->organization->id), $updatedData);

    $response->assertRedirect(route('admin.organization'))
             ->assertSessionHas('success', 'Organization updated successfully!');

    $this->organization->refresh();

    expect($this->organization->name)->toBe('Updated Organization Name');
    $this->assertFileExists(public_path('storage/logo/' . $updatedData['logo']->getClientOriginalName()));
});

it('fails to update the organization due to validation errors', function () {
    $invalidData = [
        'name' => '',
        'address' => '',
        'phoneNo' => '',
        'email' => 'invalid-email',
        'logo' => UploadedFile::fake()->image('invalid-logo.png'),
    ];

    $response = $this->put(route('admin.organization.update', $this->organization->id), $invalidData);

    $response->assertRedirect(route('admin.organization'))
             ->assertSessionHasErrors([
                 'error' => 'The organization name is required. The address is required. The phone number is required. The email must be a valid email address. The form data was retained. Please return to edit mode to continue editing the data.'
             ]);
});
