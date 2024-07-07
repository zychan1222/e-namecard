<?php

use Illuminate\Support\Facades\Hash;
use App\Models\Employee;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('user can view login form', function () {
    $response = $this->get('/login');

    $response->assertStatus(200)
        ->assertViewIs('auth.login')
        ->assertSee('Sign in to your account');
});

test('user can use register link', function () {
    $response = $this->get(route('register'));

    $response->assertStatus(200)
             ->assertViewIs('auth.register');
});

test('employee can login with valid credentials', function () {
    $employee = Employee::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->post(route('login'), [
        'email' => $employee->email,
        'password' => 'password123',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($employee);
});

test('employee cannot login with invalid credentials', function () {
    $employee = Employee::factory()->create([
        'email' => 'test2@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->post(route('login'), [
        'email' => $employee->email,
        'password' => 'wrongpassword',
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();

    $employee->delete();
});

test('inactive user cannot login', function () {
    // Create an inactive user
    $employee = Employee::factory()->create([
        'email' => 'inactive@example.com',
        'password' => bcrypt('password123'),
        'is_active' => 0,
    ]);

    // Attempt to login with the inactive user's credentials
    $response = $this->post('/login', [
        'email' => 'inactive@example.com',
        'password' => 'password123',
    ]);

    // Assert the user is redirected back to the login page
    $response->assertRedirect();

    // Assert the error message is present in the session
    $response->assertSessionHasErrors([
        'email' => 'Your account is inactive.',
    ]);

    // Assert the user is not authenticated
    $this->assertGuest();
});
