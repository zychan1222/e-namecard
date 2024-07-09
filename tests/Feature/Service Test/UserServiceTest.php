<?php

use App\Services\UserService;
use App\Repositories\UserRepository;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

test('registers a user', function () {
    // Mock UserRepository
    $userRepository = $this->mock(UserRepository::class);
    $userRepository->shouldReceive('create')->andReturn(new Employee());

    // Mock Hash facade
    Hash::shouldReceive('make')->once()->andReturn('hashed_password');

    // Mock Auth facade
    Auth::shouldReceive('login')->once();

    $userService = new UserService($userRepository);

    $user = $userService->register([
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
        'password' => 'password123',
    ]);

    expect($user)->toBeInstanceOf(Employee::class);
});

test('it logs in a user successfully', function () {
    $user = Employee::factory()->create([
        'email' => 'jane.doe@example.com',
        'password' => Hash::make('password123'),
        'is_active' => 1, // Active user
    ]);

    // Mock Auth facade for login attempt
    Auth::shouldReceive('attempt')
        ->once()
        ->with(['email' => 'jane.doe@example.com', 'password' => 'password123'])
        ->andReturn(true);

    // Mock Auth facade for retrieving authenticated user
    Auth::shouldReceive('user')->once()->andReturn($user);

    // Create an instance of UserService
    $userService = new UserService(resolve(UserRepository::class));

    // Call the login method
    $loggedInUser = $userService->login([
        'email' => 'jane.doe@example.com',
        'password' => 'password123',
    ]);

    // Assertions
    expect($loggedInUser)->toBeInstanceOf(Employee::class);
    expect($loggedInUser->id)->toBe($user->id);
});

test('updates a user profile', function () {
    // Create a test employee
    $employee = Employee::factory()->create();

    // Mock UserRepository
    $userRepository = $this->mock(UserRepository::class);
    $userRepository->shouldReceive('update')->andReturnUsing(function ($employee, $data) {
        $employee->fill($data)->save(); // Simulate update process
        return $employee;
    });

    // Mock Log facade
    Log::shouldReceive('info')->twice();

    $userService = new UserService($userRepository);

    $updatedEmployee = $userService->updateProfile($employee, [
        'name' => 'Dr. Xavier Hodkiewicz DVM', // Updated name
        // Add other fields you want to update
    ]);

    expect($updatedEmployee->name)->toBe('Dr. Xavier Hodkiewicz DVM'); // Adjusted assertion
});


test('finds a user by ID', function () {
    // Create a test employee
    $employee = Employee::factory()->create();

    // Mock UserRepository
    $userRepository = $this->mock(UserRepository::class);
    $userRepository->shouldReceive('findById')->andReturn($employee);

    $userService = new UserService($userRepository);

    $foundEmployee = $userService->findById($employee->id);

    expect($foundEmployee)->toBeInstanceOf(Employee::class);
});
