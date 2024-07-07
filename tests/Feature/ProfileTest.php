<?php

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use App\Models\Employee;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Storage;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->employee = Employee::factory()->create();
    $this->actingAs($this->employee);
});

test('employee can view profile page', function () {
    $response = $this->get(route('profile.view'));

    $response->assertStatus(200)
             ->assertSee('Profile Page')
             ->assertSee($this->employee->name);
});

test('update profile picture', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('profile.jpg');

    $response = $this->put(route('profile.update'), ['profile_pic' => $file]);

    $response->assertRedirect()
             ->assertSessionHas('success', 'Profile updated successfully');

    Storage::disk('public')->assertExists('profile_pics/' . $file->hashName());
    $this->assertDatabaseHas('employees', ['id' => $this->employee->id, 'profile_pic' => $file->hashName()]);
});

test('update profile fields', function () {
    $data = [
        'name' => 'Updated Name',
        'name_cn' => 'Updated Chinese Name',
        'phone' => '1234567890',
        'company_name' => 'New Company Name',
        'department' => 'New Department',
        'designation' => 'New Designation',
    ];

    foreach ($data as $field => $value) {
        $response = $this->put(route('profile.update'), [$field => $value]);

        $response->assertRedirect()
                 ->assertSessionHas('success', 'Profile updated successfully');

        $this->assertDatabaseHas('employees', ['id' => $this->employee->id, $field => $value]);
    }
});

test('employee cannot update profile with invalid data', function () {
    $response = $this->put(route('profile.update'), ['name' => '']);

    $response->assertSessionHasErrors();
});

test('employee can cancel profile update', function () {
    // Simulate canceling profile update
    $response = $this->put(route('profile.update'), ['name' => $this->employee->name]);

    $response = $this->get(route('profile.view'));

    $response->assertStatus(200)
             ->assertDontSee('Updated Name');
});

test('employee cannot update profile with empty name field', function () {
    $response = $this->put(route('profile.update'), ['name' => '']);

    $response->assertSessionHasErrors();
});
