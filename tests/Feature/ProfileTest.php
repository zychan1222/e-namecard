<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use App\Services\ProfileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->employee = Employee::factory()->create(['user_id' => $this->user->id]);
    session(['employee_id' => $this->employee->id]);
    $this->profileService = mock(ProfileService::class);
    app()->instance(ProfileService::class, $this->profileService);
});

it('views profile and returns profile view with employee data', function () {
    $this->profileService->shouldReceive('getEmployeeById')
        ->once()
        ->with($this->employee->id)
        ->andReturn($this->employee);

    $this->profileService->shouldReceive('getUserEmail')
        ->once()
        ->with($this->employee->user_id)
        ->andReturn('user@example.com');

    $response = $this->actingAs($this->user)->get(route('profile.view'));

    $response->assertStatus(200)
             ->assertViewIs('profile')
             ->assertViewHas('employee', $this->employee)
             ->assertViewHas('email', 'user@example.com');
});

it('redirects to login if employee not found when viewing profile', function () {
    $this->profileService->shouldReceive('getEmployeeById')
        ->once()
        ->andReturn(null);

    $response = $this->actingAs($this->user)->get(route('profile.view'));

    $response->assertRedirect(route('login'))
             ->assertSessionHas('error', 'Employee not found.');
});

it('returns edit view with employee data', function () {
    $this->profileService->shouldReceive('getEmployeeById')
        ->once()
        ->with($this->employee->id)
        ->andReturn($this->employee);

    $this->profileService->shouldReceive('getUserEmail')
        ->once()
        ->with($this->employee->user_id)
        ->andReturn('user@example.com');

    $response = $this->actingAs($this->user)->get(route('profile.view'));

    $response->assertStatus(200)
             ->assertViewIs('profile')
             ->assertViewHas('employee', $this->employee)
             ->assertViewHas('email', 'user@example.com');
});

it('updates profile and returns success message on valid input', function () {
    $data = [
        'name' => 'New Name',
        'phone' => '9876543210',
        'profile_pic' => UploadedFile::fake()->image('profile.jpg')
    ];

    $this->profileService->shouldReceive('getEmployeeById')
        ->once()
        ->with($this->employee->id)
        ->andReturn($this->employee);

    $this->profileService->shouldReceive('updateProfile')
        ->once()
        ->with($this->employee, $data)
        ->andReturn(true);

    $response = $this->actingAs($this->user)->put(route('profile.update'), $data);

    $response->assertRedirect()
             ->assertSessionHas('success', 'Profile updated successfully!');
});

it('returns error message on exception during profile update', function () {
    $data = [
        'name' => 'New Name',
        'phone' => '9876543210',
        'profile_pic' => UploadedFile::fake()->image('profile.jpg')
    ];

    $this->profileService->shouldReceive('getEmployeeById')
        ->once()
        ->with($this->employee->id)
        ->andReturn($this->employee);

    $this->profileService->shouldReceive('updateProfile')
        ->once()
        ->with($this->employee, $data)
        ->andThrow(new \Exception('Update error'));

    $response = $this->actingAs($this->user)->put(route('profile.update'), $data);

    $response->assertRedirect()
             ->assertSessionHas('error', 'An error occurred while updating the profile.');
});
