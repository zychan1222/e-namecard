<?php

use App\Repositories\EmployeeRepository;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->repository = new EmployeeRepository();
});

it('can create an employee', function () {
    $data = ['name' => 'John Doe', 'user_id' => 1]; // Ensure the user with ID 1 exists

    $employee = $this->repository->create($data);

    expect($employee)->toBeInstanceOf(Employee::class);
    expect($employee->name)->toEqual($data['name']);
});

it('can find an employee by id', function () {
    $employee = Employee::factory()->create();

    $foundEmployee = $this->repository->findById($employee->id);

    expect($foundEmployee)->toBeInstanceOf(Employee::class);
    expect($foundEmployee->id)->toEqual($employee->id);
});

it('returns null when employee not found by id', function () {
    $foundEmployee = $this->repository->findById(999);

    expect($foundEmployee)->toBeNull();
});

it('can find employees by user id', function () {
    $user = \App\Models\User::factory()->create(); // Ensure you have a user factory
    $employee1 = Employee::factory()->create(['user_id' => $user->id]);
    $employee2 = Employee::factory()->create(['user_id' => $user->id]);

    $foundEmployees = $this->repository->findByUserId($user->id);

    expect($foundEmployees)->toHaveCount(2);
    // Check by ID to see if the employee is present in the collection
    expect($foundEmployees->pluck('id'))->toContain($employee1->id);
    expect($foundEmployees->pluck('id'))->toContain($employee2->id);
});

it('returns an empty collection when no employees found by user id', function () {
    $foundEmployees = $this->repository->findByUserId(999);

    expect($foundEmployees)->toBeEmpty();
});

it('can update an employee', function () {
    $employee = Employee::factory()->create(['name' => 'Old Name']);

    $this->repository->update($employee, ['name' => 'New Name']);

    $employee->refresh();

    expect($employee->name)->toEqual('New Name');
});