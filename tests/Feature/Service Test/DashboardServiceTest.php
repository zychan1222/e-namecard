<?php

use App\Services\DashboardService;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->dashboardService = new DashboardService();
    // Mock the Auth facade
    $this->userId = 1;
    Auth::shouldReceive('id')->andReturn($this->userId);
});

it('retrieves the authenticated employee', function () {
    $employee = Employee::factory()->create(['user_id' => $this->userId]);

    $result = $this->dashboardService->getAuthenticatedEmployee($employee->id);

    expect($result->id)->toEqual($employee->id);
    expect($result->name)->toEqual($employee->name);
    expect($result->phone)->toEqual($employee->phone);
    expect($result->department)->toEqual($employee->department);
    expect($result->designation)->toEqual($employee->designation);
    expect($result->is_active)->toEqual($employee->is_active);
});

it('returns null for an employee not belonging to the authenticated user', function () {
    $employee = Employee::factory()->create(['user_id' => 2]);

    $result = $this->dashboardService->getAuthenticatedEmployee($employee->id);

    expect($result)->toBeNull();
});

it('logs dashboard access attempt', function () {
    Log::shouldReceive('info')
        ->once()
        ->with('Dashboard access attempt.', [
            'user_id' => $this->userId,
            'employee_id' => 1,
            'session_data' => session()->all(),
        ]);

    $this->dashboardService->logDashboardAccess(1);
});

it('logs employee retrieval result when employee exists', function () {
    $employee = Employee::factory()->create(['user_id' => $this->userId]);

    Log::shouldReceive('info')
        ->once()
        ->with('Employee retrieval result.', [
            'employee_exists' => true,
            'employee_data' => $employee->toArray(),
        ]);

    $this->dashboardService->logEmployeeRetrieval($employee);
});

it('logs employee retrieval result when employee does not exist', function () {
    Log::shouldReceive('info')
        ->once()
        ->with('Employee retrieval result.', [
            'employee_exists' => false,
            'employee_data' => null,
        ]);

    $this->dashboardService->logEmployeeRetrieval(null);
});
