<?php
use App\Repositories\EmployeeProfileRepository;
use App\Models\Admin;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->repository = new EmployeeProfileRepository();
});

it('can find an admin by id', function () {
    $admin = Admin::factory()->create();

    $foundAdmin = $this->repository->findAdminById($admin->id);

    expect($foundAdmin)->toBeInstanceOf(Admin::class);
    expect($foundAdmin->id)->toEqual($admin->id);
});

it('returns null when admin not found by id', function () {
    $foundAdmin = $this->repository->findAdminById(999);

    expect($foundAdmin)->toBeNull();
});

it('can find an employee by id', function () {
    $employee = Employee::factory()->create();

    $foundEmployee = $this->repository->findEmployeeById($employee->id);

    expect($foundEmployee)->toBeInstanceOf(Employee::class);
    expect($foundEmployee->id)->toEqual($employee->id);
});

it('returns null when employee not found by id', function () {
    $foundEmployee = $this->repository->findEmployeeById(999);

    expect($foundEmployee)->toBeNull();
});

it('can find a user by email', function () {
    $user = User::factory()->create();

    $foundUser = $this->repository->findUserByEmail($user->email);

    expect($foundUser)->toBeInstanceOf(User::class);
    expect($foundUser->email)->toEqual($user->email);
});

it('returns null when user not found by email', function () {
    $foundUser = $this->repository->findUserByEmail('nonexistent@example.com');

    expect($foundUser)->toBeNull();
});

it('can create a user', function () {
    $data = ['email' => 'newuser@example.com'];

    $user = $this->repository->createUser($data);

    expect($user)->toBeInstanceOf(User::class);
    expect($user->email)->toEqual($data['email']);
});

it('can create an employee', function () {
    $data = ['name' => 'John Doe', 'user_id' => 1]; // Ensure the user with ID 1 exists

    $employee = $this->repository->createEmployee($data);

    expect($employee)->toBeInstanceOf(Employee::class);
    expect($employee->name)->toEqual($data['name']);
});

it('can update an employee', function () {
    $employee = Employee::factory()->create(['name' => 'Old Name']);

    $this->repository->updateEmployee($employee, ['name' => 'New Name']);

    $employee->refresh();

    expect($employee->name)->toEqual('New Name');
});

it('can find an admin by employee id', function () {
    $employee = Employee::factory()->create();
    $admin = Admin::factory()->create(['employee_id' => $employee->id]);

    $foundAdmin = $this->repository->findAdminByEmployeeId($employee->id);

    expect($foundAdmin)->toBeInstanceOf(Admin::class);
    expect($foundAdmin->id)->toEqual($admin->id);
});

it('returns null when admin not found by employee id', function () {
    $foundAdmin = $this->repository->findAdminByEmployeeId(999);

    expect($foundAdmin)->toBeNull();
});

it('can delete an admin', function () {
    $admin = Admin::factory()->create();

    $this->repository->deleteAdmin($admin);

    $this->assertNull(Admin::find($admin->id));
});

it('can delete an employee', function () {
    $employee = Employee::factory()->create();

    $this->repository->deleteEmployee($employee->id);

    $this->assertNull(Employee::find($employee->id));
});

it('can find an employee by user id and company id', function () {
    $user = User::factory()->create();
    $employee = Employee::factory()->create(['user_id' => $user->id, 'company_id' => 1]);

    $foundEmployee = $this->repository->findEmployeeByUserIdAndCompanyId($user->email, 1);

    expect($foundEmployee)->toBeInstanceOf(Employee::class);
    expect($foundEmployee->id)->toEqual($employee->id);
});

it('returns null when employee not found by user id and company id', function () {
    $foundEmployee = $this->repository->findEmployeeByUserIdAndCompanyId('nonexistent@example.com', 1);

    expect($foundEmployee)->toBeNull();
});
