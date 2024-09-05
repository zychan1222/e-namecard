<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Organization;
use App\Models\Employee;
use App\Services\UserService;
use App\Services\EmployeeService;
use App\Services\OrganizationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->userService = $this->createMock(UserService::class);
    $this->employeeService = $this->createMock(EmployeeService::class);
    $this->organizationService = $this->createMock(OrganizationService::class);

    // Bind the mocks to the service container
    $this->app->instance(UserService::class, $this->userService);
    $this->app->instance(EmployeeService::class, $this->employeeService);
    $this->app->instance(OrganizationService::class, $this->organizationService);
});

it('shows the email registration form', function () {
    $response = $this->get(route('register.email.form'));
    
    $response->assertStatus(200)
             ->assertViewIs('auth.email-registration');
});

it('registers an email successfully', function () {
    $email = 'test@example.com';

    $this->userService
        ->expects($this->once())
        ->method('registerEmail')
        ->with($email)
        ->willReturn((object)['id' => 1]);

    $response = $this->post(route('register.email.store'), ['email' => $email]);

    $response->assertRedirect(route('verify.tac.form', ['email' => $email]));
    $this->assertEquals(1, Session::get('user_id'));
    $response->assertSessionHas('success', 'TAC sent to your email.');
});

it('shows the TAC verification form', function () {
    $email = 'test@example.com';
    
    $response = $this->get(route('verify.tac.form', ['email' => $email]));

    $response->assertStatus(200)
             ->assertViewIs('auth.verify-tac');
});

it('verifies TAC successfully', function () {
    $email = 'test@example.com';
    $tac = '123456';

    $this->userService
        ->expects($this->once())
        ->method('verifyTAC')
        ->with($email, $tac)
        ->willReturn(true);

    $response = $this->post(route('verify.tac.store', ['email' => $email]), ['tac' => $tac]);

    $response->assertRedirect(route('register.organization'));
    $response->assertSessionHas('success', 'TAC verified successfully. Proceed to organization registration.');
});

it('does not verify TAC and redirects with an error for invalid TAC', function () {
    $email = 'test@example.com';
    $tac = 'invalid_tac';

    $this->userService
        ->expects($this->once())
        ->method('verifyTAC')
        ->with($email, $tac)
        ->willReturn(false);

    $response = $this->post(route('verify.tac.store', ['email' => $email]), ['tac' => $tac]);

    $response->assertRedirect(route('verify.tac.form', ['email' => $email]));
    $response->assertSessionHasErrors(['tac' => 'Invalid TAC.']);
});

it('shows the organization registration form', function () {
    $response = $this->get(route('register.organization'));
    
    $response->assertStatus(200)
             ->assertViewIs('auth.organization-registration');
});

it('registers an organization successfully', function () {
    $organizationData = [
        'name' => 'Test Organization',
        'address' => '123 Test St',
        'email' => 'test@organization.com',
        'phoneNo' => '1234567890',
    ];

    Session::put('user_id', 1);
    Session::put('employee_id', 1);

    $this->organizationService
        ->expects($this->once())
        ->method('registerOrganization')
        ->with($organizationData, 1, 1)
        ->willReturn((object)['id' => 1]);

    $this->employeeService
        ->expects($this->once())
        ->method('updateEmployeeCompany')
        ->with(1, 1);

    $response = $this->post(route('register.organization'), $organizationData);

    $response->assertRedirect(route('organization.created'));
    $this->assertNull(Session::get('user_id'));
    $this->assertNull(Session::get('employee_id'));
    $response->assertSessionHas('success', 'Organization registered successfully!');
});

it('registers an admin successfully', function () {
    $user = User::factory()->create(['email' => 'admin@example.com']);
    $organization = Organization::factory()->create(['name' => 'Test Organization']);
    $employee = Employee::factory()->create(['user_id' => $user->id, 'company_id' => $organization->id]);

    $adminData = [
        'name' => 'Admin',
        'email' => 'admin@example.com',
        'user_id' => $user->id,
        'company_id' => $organization->id,
        'is_active' => true,
    ];

    Session::put('organization_id', $organization->id);

    $this->organizationService
        ->expects($this->once())
        ->method('registerAdmin')
        ->with($adminData);

    $response = $this->post(route('register.admin'), $adminData);

    $response->assertRedirect(route('organization.created'));
    $this->assertNull(Session::get('organization_id'));
    $response->assertSessionHas('success', 'Admin registered successfully!');
});

it('shows the organization created page', function () {
    $response = $this->get(route('organization.created'));

    $response->assertStatus(200)
             ->assertViewIs('organization-created');
});