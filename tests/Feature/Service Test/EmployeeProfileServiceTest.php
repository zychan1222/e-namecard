<?php

use App\Services\EmployeeProfileService;
use App\Repositories\EmployeeProfileRepository;
use App\Mail\AccountCreatedNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Organization;
use App\Models\User;
use App\Models\Employee;
use App\Models\Admin;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->organization = Organization::factory()->create();
    $this->user = User::factory()->create();
    $this->employee = Employee::factory()->create(['user_id' => $this->user->id, 'company_id' => $this->organization->id]);
    $this->admin = Admin::factory()->create(['employee_id' => $this->employee->id]);

    $this->employeeProfileRepository = Mockery::mock(EmployeeProfileRepository::class);
    $this->employeeProfileService = new EmployeeProfileService($this->employeeProfileRepository);
});

afterEach(function () {
    Mockery::close();
});

it('returns employee profile data', function () {
    $adminId = $this->admin->id;
    $employeeId = $this->employee->id;

    $this->employeeProfileRepository->shouldReceive('findAdminById')->with($adminId)->andReturn($this->admin);
    $this->employeeProfileRepository->shouldReceive('findEmployeeById')->with($employeeId)->andReturn($this->employee);

    $result = $this->employeeProfileService->getEmployeeProfileData($adminId, $employeeId);

    expect($result['employee'])->toBe($this->employee);
    expect($result['pageTitle'])->toBe('Employee Profile Page');
    expect($result['editMode'])->toBe(false);
});

it('returns data for creating employee', function () {
    $adminId = $this->admin->id;

    $this->employeeProfileRepository->shouldReceive('findAdminById')->with($adminId)->andReturn($this->admin);
    $this->employeeProfileRepository->shouldReceive('findEmployeeById')->with($this->admin->employee_id)->andReturn($this->employee);

    $result = $this->employeeProfileService->getCreateEmployeeData($adminId);

    expect($result['pageTitle'])->toBe('Create Employee Profile');
    expect($result['organization']->id)->toBe($this->organization->id);
    expect($result['adminEmployee']->id)->toBe($this->employee->id);
});

it('stores a new employee and sends notification', function () {
    $adminId = $this->admin->id;
    $data = [
        'email' => 'test@example.com',
        'name' => 'Test Employee',
        'name_cn' => '测试员工',
        'phone' => '1234567890',
        'department' => 'IT',
        'designation' => 'Developer',
        'is_active' => true,
    ];

    $user = (object)['id' => 2];
    $employee = (object)['id' => 3, 'user_id' => $user->id, 'company_id' => $this->admin->company_id];

    Mail::fake();

    $this->employeeProfileRepository->shouldReceive('findUserByEmail')->with($data['email'])->andReturn(null);
    $this->employeeProfileRepository->shouldReceive('createUser')->with(['email' => $data['email']])->andReturn($user);
    $this->employeeProfileRepository->shouldReceive('findAdminById')->with($adminId)->andReturn($this->admin);
    $this->employeeProfileRepository->shouldReceive('findEmployeeById')->with($this->admin->employee_id)->andReturn((object)['company_id' => $this->admin->company_id]);
    $this->employeeProfileRepository->shouldReceive('createEmployee')->with(Mockery::on(function ($data) use ($user) {
        return $data['user_id'] == $user->id && $data['company_id'] == $this->admin->company_id;
    }))->andReturn($employee);

    $this->employeeProfileService->storeEmployee($data, $adminId);

    Mail::assertSent(AccountCreatedNotification::class, function ($mail) use ($employee) {
        return $mail->hasTo('test@example.com') && $mail->employee->id == $employee->id;
    });
});

// Test for destroyEmployee - self deletion
it('throws exception when trying to delete own profile', function () {
    $adminId = $this->admin->id;
    $employeeId = $this->employee->id;

    $this->employeeProfileRepository->shouldReceive('findAdminById')->with($adminId)->andReturn($this->admin);

    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('You cannot delete your own profile.');

    $this->employeeProfileService->destroyEmployee($adminId, $employeeId);
});