<?php

use App\Models\Admin;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\User;
use App\Services\AuthService;
use App\Repositories\UserRepository;
use App\Repositories\EmployeeRepository;
use App\Repositories\AdminRepository;
use App\Services\TACService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->organization = Organization::factory()->create();
    $this->user = User::factory()->create();
    $this->employee = Employee::factory()->create([
        'user_id' => $this->user->id,
        'company_id' => $this->organization->id
    ]);
    $this->admin = Admin::factory()->create(['employee_id' => $this->employee->id]);

    $this->userRepository = Mockery::mock(UserRepository::class);
    $this->employeeRepository = Mockery::mock(EmployeeRepository::class);
    $this->adminRepository = Mockery::mock(AdminRepository::class);
    $this->tacService = Mockery::mock(TACService::class);

    $this->authService = new AuthService(
        $this->userRepository,
        $this->employeeRepository,
        $this->adminRepository,
        $this->tacService
    );
});

it('sends TAC to user if found', function () {
    $email = 'user@example.com';
    $user = (object)['email' => $email];
    $tacCode = '123456';

    $this->userRepository->shouldReceive('findByEmail')
        ->with($email)
        ->andReturn($user);

    $this->tacService->shouldReceive('generateTAC')->andReturn($tacCode);
    $this->tacService->shouldReceive('sendTAC')->with($user, $tacCode);

    $result = $this->authService->generateAndSendTAC($email);

    expect($result)->toBe($user);
});

it('returns null if user not found when sending TAC', function () {
    $email = 'notfound@example.com';

    $this->userRepository->shouldReceive('findByEmail')
        ->with($email)
        ->andReturn(null);

    $result = $this->authService->generateAndSendTAC($email);

    expect($result)->toBeNull();
});

it('returns user if TAC is valid', function () {
    $email = 'user@example.com';
    $tacCode = '123456';
    $user = (object)['email' => $email, 'tac_code' => $tacCode, 'tac_expiry' => now()->addMinutes(5)];

    $this->userRepository->shouldReceive('findByEmail')
        ->with($email)
        ->andReturn($user);

    $result = $this->authService->authenticateUser($email, $tacCode);

    expect($result)->toBe($user);
});

it('returns null if TAC is invalid', function () {
    $email = 'user@example.com';
    $invalidTacCode = '654321';
    $user = (object)['email' => $email, 'tac_code' => '123456', 'tac_expiry' => now()->addMinutes(5)];

    $this->userRepository->shouldReceive('findByEmail')
        ->with($email)
        ->andReturn($user);

    $result = $this->authService->authenticateUser($email, $invalidTacCode);

    expect($result)->toBeNull();
});

it('returns employee entries for user', function () {
    $userId = $this->user->id;
    $employeeEntries = [(object)['id' => 1, 'user_id' => $userId]];

    $this->employeeRepository->shouldReceive('findByUserId')
        ->with($userId)
        ->andReturn($employeeEntries);

    $result = $this->authService->getEmployeeEntries($userId);

    expect($result)->toBe($employeeEntries);
});

it('returns admin details if found', function () {
    $employeeId = $this->employee->id;
    $admin = (object)['id' => 1, 'employee_id' => $employeeId];

    $this->adminRepository->shouldReceive('findByEmployeeId')
        ->with($employeeId)
        ->andReturn($admin);

    $result = $this->authService->findAdmin($employeeId);

    expect($result)->toBe($admin);
});

it('returns employee details if found', function () {
    $employeeId = $this->employee->id;
    $employee = (object)['id' => $employeeId];

    $this->employeeRepository->shouldReceive('findById')
        ->with($employeeId)
        ->andReturn($employee);

    $result = $this->authService->findEmployee($employeeId);

    expect($result)->toBe($employee);
});
