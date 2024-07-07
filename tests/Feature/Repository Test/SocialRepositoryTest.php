<?php

use App\Repositories\SocialRepository;
use App\Models\Employee;
use App\Models\SocialConnection;
use Laravel\Socialite\Contracts\User as SocialUser;


uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->socialRepository = new SocialRepository();
});

test('find employee by email', function () {
    $employee = Employee::factory()->create([
        'email' => 'socialconnection@example.com'
    ]);

    $foundEmployee = $this->socialRepository->findEmployeeByEmail('socialconnection@example.com');

    expect($foundEmployee)->toBeInstanceOf(Employee::class);
    expect($foundEmployee->email)->toEqual('socialconnection@example.com');
});

test('find employee by email returns null for non existing email', function () {
    $foundEmployee = $this->socialRepository->findEmployeeByEmail('non.existing@example.com');

    expect($foundEmployee)->toBeNull();
});

test('create employee', function () {
    $employeeData = [
        'name' => 'socialconnection2',
        'email' => 'socialconnection2@example.com',
        'password' => bcrypt('password123'),
    ];

    $employee = $this->socialRepository->createEmployee($employeeData);

    expect($employee)->toBeInstanceOf(Employee::class);
    $this->assertDatabaseHas('employees', [
        'name' => 'socialconnection2',
        'email' => 'socialconnection2@example.com',
    ]);
});

test('create social connection', function () {
    $employee = Employee::factory()->create();
    $socialUser = \Mockery::mock(SocialUser::class);
    $socialUser->shouldReceive('getId')->andReturn('1234567890');
    $socialUser->token = 'fake_token';

    $provider = 'google';

    $this->socialRepository->createSocialConnection($employee, $socialUser, $provider);

    $this->assertDatabaseHas('social_connections', [
        'employee_id' => $employee->id,
        'provider' => 'google',
        'provider_id' => '1234567890',
        'access_token' => 'fake_token',
    ]);
});

afterEach(function () {
    \Mockery::close();
});
