<?php

use App\Repositories\UserRepository;
use App\Models\Employee;


uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->userRepository = new UserRepository();
});

test('create employee', function () {
    $data = [
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
        'password' => bcrypt('password123'),
    ];

    $employee = $this->userRepository->create($data);

    expect($employee)->toBeInstanceOf(Employee::class);
    $this->assertDatabaseHas('employees', [
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
    ]);
});

test('find by id', function () {
    $employee = Employee::factory()->create();

    $foundEmployee = $this->userRepository->findById($employee->id);

    expect($foundEmployee)->toBeInstanceOf(Employee::class);
    expect($foundEmployee->id)->toEqual($employee->id);
});

test('find by id returns null for non existing id', function () {
    $foundEmployee = $this->userRepository->findById(999);

    expect($foundEmployee)->toBeNull();
});

test('find by email', function () {
    $employee = Employee::factory()->create([
        'email' => 'jane.doe@example.com'
    ]);

    $foundEmployee = $this->userRepository->findByEmail('jane.doe@example.com');

    expect($foundEmployee)->toBeInstanceOf(Employee::class);
    expect($foundEmployee->email)->toEqual('jane.doe@example.com');
});

test('find by email returns null for non existing email', function () {
    $foundEmployee = $this->userRepository->findByEmail('non.existing@example.com');

    expect($foundEmployee)->toBeNull();
});
