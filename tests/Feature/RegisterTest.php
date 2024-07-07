<?php

use App\Models\Employee;
use Illuminate\Support\Facades\Hash;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('registration form displayed', function () {
    $response = $this->get('/register');

    $response->assertStatus(200)
             ->assertViewIs('auth.register');
});

test('user can use login link', function () {
    $response = $this->get(route('login'));

    $response->assertStatus(200)
             ->assertViewIs('auth.login');
});

test('user can register with valid data', function () {
    $uniqueEmail = 'test' . time() . '@example.com';

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => $uniqueEmail,
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticated();
    $this->assertDatabaseHas('employees', [
        'email' => $uniqueEmail,
    ]);
});

test('user cannot register with existing email', function () {
    Employee::create([
        'name' => 'Existing User',
        'email' => 'existing@example.com',
        'password' => Hash::make('password'),
    ]);

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'existing@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors(['email']);
    $this->assertGuest();
});

test('user cannot register with passwords that don\'t match', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'not-matching',
    ]);

    $response->assertSessionHasErrors(['password']);
    $this->assertGuest();
});
