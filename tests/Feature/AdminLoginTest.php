<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Organization;
use App\Models\Employee;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->authService = $this->createMock(AuthService::class);
    $this->app->instance(AuthService::class, $this->authService);
});

it('shows the admin login form', function () {
    $response = $this->get(route('admin.login.form'));
    $response->assertStatus(200)->assertViewIs('auth.admin-login');
});

it('sends TAC for existing admin user and redirects', function () {
    $email = 'admin@example.com';
    $user = User::factory()->make(['id' => 1]);

    $this->authService
        ->expects($this->once())
        ->method('generateAndSendTAC')
        ->with($email)
        ->willReturn($user);

    $response = $this->post(route('admin.login.sendTAC'), ['email' => $email]);

    $response->assertRedirect(route('admin.login.tac.show'));
    $this->assertEquals($email, session('email'));
    $response->assertSessionHas('success', 'TAC sent successfully to your email.');
});

it('does not send TAC for non-existent admin user', function () {
    $email = 'nonexistent@example.com';

    $this->authService
        ->expects($this->once())
        ->method('generateAndSendTAC')
        ->with($email)
        ->willReturn(null);

    $response = $this->post(route('admin.login.sendTAC'), ['email' => $email]);

    $response->assertRedirect(route('admin.login.form'));
    $response->assertSessionHasErrors(['email' => 'No user found with this email.']);
});

it('shows the TAC form for admin', function () {
    $response = $this->get(route('admin.login.tac.show'));
    $response->assertStatus(200)->assertViewIs('auth.admin-tac');
});

it('logs in admin with valid TAC', function () {
    $email = 'admin3@example.com';
    $tacCode = '123456';
    $user = User::factory()->make(['id' => 1]);

    $this->authService
        ->expects($this->once())
        ->method('authenticateUser')
        ->with($email, $tacCode)
        ->willReturn($user);

    $this->authService
        ->expects($this->once())
        ->method('getEmployeeEntries')
        ->with($user->id)
        ->willReturn(collect([(object)['id' => 1, 'company_id' => 1]]));

    $response = $this->post(route('admin.login.tac'), [
        'email' => $email,
        'tac_code' => $tacCode,
    ]);

    $response->assertRedirect(route('admin.select.organization'));
    $this->assertEquals(1, Session::get('employeeEntries')[0]->id);
    $response->assertSessionHas('success', 'Logged in successfully.');
});

it('does not log in admin with invalid TAC', function () {
    $email = 'admin4@example.com';
    $tacCode = 'wrong_code';

    $this->authService
        ->expects($this->once())
        ->method('authenticateUser')
        ->with($email, $tacCode)
        ->willReturn(null);

    $response = $this->post(route('admin.login.tac'), [
        'email' => $email,
        'tac_code' => $tacCode,
    ]);

    $response->assertRedirect(route('admin.login.tac.show'));
    $response->assertSessionHasErrors(['tac_code' => 'Invalid TAC code or it has expired.']);
});

it('shows the admin organization selection form', function () {
    $organization = Organization::factory()->create();
    $employee = Employee::factory()->create(['company_id' => $organization->id]);
    Session::put('employeeEntries', [$employee]);

    $response = $this->get(route('admin.select.organization'));

    $response->assertStatus(200)
        ->assertViewIs('auth.admin-select_organization')
        ->assertSee('Select Organization');
});

it('selects an organization as admin and redirects to the dashboard', function () {
    $organization = Organization::factory()->create();
    $employee = Employee::factory()->create(['company_id' => $organization->id]);
    Session::put('employeeEntries', [$employee]);

    $this->authService
        ->expects($this->once())
        ->method('findEmployee')
        ->with($employee->id)
        ->willReturn($employee);

    $admin = (object)['id' => 1, 'user_id' => $employee->user_id];
    $this->authService
        ->expects($this->once())
        ->method('findAdmin')
        ->with($employee->id)
        ->willReturn($admin);

    $response = $this->post(route('admin.select.organization.submit'), ['employee_id' => $employee->id]);

    $response->assertRedirect(route('admin.dashboard'));
    $this->assertEquals($employee->id, Session::get('employee_id'));
    $this->assertEquals($employee->company_id, Session::get('company_id'));
    $this->assertEquals($admin->id, Session::get('admin_id'));
    $this->assertNotNull(Session::get('current_employee'));
    $response->assertSessionHas('success', 'Organization selected successfully.');
});

it('logs out the admin and redirects to the home page', function () {
    Auth::loginUsingId(1);

    $response = $this->post(route('adminlogout'));

    $response->assertRedirect('/');
    $this->assertFalse(Auth::guard('admin')->check());
    $this->assertNull(Session::get('employeeEntries'));
    $response->assertSessionHas('success', 'Logged out successfully.');
});
