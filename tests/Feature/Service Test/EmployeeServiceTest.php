<?php

use App\Repositories\EmployeeRepository;
use App\Services\EmployeeService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->employeeRepository = Mockery::mock(EmployeeRepository::class);
    $this->employeeService = new EmployeeService($this->employeeRepository);
});

it('can find employee by user id', function () {
    $userId = 1;
    $expectedEmployee = ['id' => 1, 'name' => 'John Doe'];

    $this->employeeRepository
        ->shouldReceive('findByUserId')
        ->with($userId)
        ->andReturn($expectedEmployee);

    $result = $this->employeeService->findByUserId($userId);

    expect($result)->toBe($expectedEmployee);
});

it('can create a new employee', function () {
    $data = ['name' => 'John Doe', 'email' => 'john@example.com'];

    $this->employeeRepository
        ->shouldReceive('create')
        ->with($data)
        ->andReturn($data);

    $result = $this->employeeService->create($data);

    expect($result)->toBe($data);
});

it('can find employee by id', function () {
    $employeeId = 1;
    $expectedEmployee = ['id' => 1, 'name' => 'John Doe'];

    $this->employeeRepository
        ->shouldReceive('findById')
        ->with($employeeId)
        ->andReturn($expectedEmployee);

    $result = $this->employeeService->findById($employeeId);

    expect($result)->toBe($expectedEmployee);
});

it('can update employee company', function () {
    $employeeId = 1;
    $companyId = 2;
    $employee = ['id' => $employeeId, 'name' => 'John Doe'];

    $this->employeeRepository
        ->shouldReceive('findById')
        ->with($employeeId)
        ->andReturn($employee);

    $this->employeeRepository
        ->shouldReceive('update')
        ->with($employee, ['company_id' => $companyId])
        ->andReturn(true);

    $result = $this->employeeService->updateEmployeeCompany($employeeId, $companyId);

    expect($result)->toBe(true);
});