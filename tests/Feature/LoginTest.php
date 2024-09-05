<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Organization;
use App\Models\Employee;
use App\Services\UserService;
use App\Services\EmployeeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->userService = $this->mock(UserService::class);
    $this->employeeService = $this->mock(EmployeeService::class);
});

it('shows the login form', function () {
    $response = $this->get(route('login'));

    $response->assertStatus(200)
             ->assertViewIs('auth.login');
});

it('sends TAC and redirects to the TAC form', function () {
    $email = 'test@example.com';
    $user = User::factory()->make(['id' => 1]);

    $this->userService->shouldReceive('generateAndSendTAC')
                      ->once()
                      ->with($email)
                      ->andReturn($user);

    $response = $this->post(route('login.sendTAC'), ['email' => $email]);

    $response->assertRedirect(route('login.tac.show'))
             ->assertSessionHas('success', 'TAC sent successfully to your email.');
});

it('redirects with an error for non-existent user', function () {
    $invalidEmail = 'nonexistent@example.com';

    $this->userService->shouldReceive('generateAndSendTAC')
                      ->once()
                      ->with($invalidEmail)
                      ->andReturn(null);

    $response = $this->post(route('login.sendTAC'), ['email' => $invalidEmail]);

    $response->assertRedirect(route('login'))
             ->assertSessionHasErrors(['email' => 'No user found with this email.']);
});

it('shows the TAC form', function () {
    $response = $this->get(route('login.tac.show'));

    $response->assertStatus(200)
             ->assertViewIs('auth.tac');
});

it('logs in with valid TAC', function () {
    $email = 'test@example.com';
    $tacCode = '123456';
    $user = User::factory()->make(['id' => 1]);

    $this->userService->shouldReceive('verifyTAC')
                      ->once()
                      ->with($email, $tacCode)
                      ->andReturn(true);

    $this->userService->shouldReceive('findByEmail')
                      ->once()
                      ->with($email)
                      ->andReturn($user);

    $this->employeeService->shouldReceive('findByUserId')
                          ->once()
                          ->with(1)
                          ->andReturn(collect([(object)['id' => 1, 'company_id' => 1]]));

    $response = $this->post(route('login.tac'), [
        'email' => $email,
        'tac_code' => $tacCode
    ]);

    $response->assertRedirect(route('select.organization'))
             ->assertSessionHas('success', 'Login successful.');
});

it('does not log in with invalid TAC', function () {
    $email = 'test@example.com';
    $tacCode = 'wrong_code';

    $this->userService->shouldReceive('verifyTAC')
                      ->once()
                      ->with($email, $tacCode)
                      ->andReturn(false);

    $response = $this->post(route('login.tac'), [
        'email' => $email,
        'tac_code' => $tacCode
    ]);

    $response->assertRedirect(route('login.tac.show'))
             ->assertSessionHasErrors(['tac_code' => 'Invalid TAC code or it has expired.']);
});

it('displays the organization selection page with employee entries', function () {
    $organization = Organization::factory()->create();
    $employee = Employee::factory()->create(['company_id' => $organization->id]);

    Session::put('employeeEntries', [$employee]);

    $response = $this->get(route('select.organization'));

    $response->assertStatus(200)
             ->assertSee($organization->name)
             ->assertSee('Select Your Organization');
});

it('displays a warning when there are no employee entries', function () {
    Session::put('employeeEntries', []);

    $response = $this->get(route('select.organization'));

    $response->assertStatus(200)
             ->assertSee('No employee entries available');
});

it('redirects with an error for invalid employee selection', function () {
    $invalidEmployeeId = 999;

    $this->employeeService->shouldReceive('findById')
                          ->once()
                          ->with($invalidEmployeeId)
                          ->andReturn(null);

    $response = $this->post(route('select.organization.post'), ['employee_id' => $invalidEmployeeId]);

    $response->assertRedirect(route('select.organization'))
             ->assertSessionHasErrors(['employee_id' => 'Invalid selection.']);
});

it('selects an organization and redirects to the dashboard', function () {
    $organization = (object)['name' => 'Test Organization', 'logo' => null];
    $employee = (object)['id' => 1, 'organization' => $organization, 'company_id' => 1];

    Session::put('employeeEntries', collect([$employee]));

    $this->employeeService->shouldReceive('findById')
                          ->once()
                          ->with(1)
                          ->andReturn((object)['id' => 1, 'user_id' => Auth::id(), 'company_id' => 1]);

    $response = $this->post(route('select.organization.post'), ['employee_id' => 1]);

    $response->assertRedirect(route('dashboard'))
             ->assertSessionHas('success', 'Organization selected successfully.');
    $this->assertEquals(1, Session::get('employee_id'));
});

it('logs out the user and redirects to the home page', function () {
    Auth::login(User::factory()->create());

    $response = $this->post(route('logout'));

    $response->assertRedirect('/')
             ->assertSessionHas('success', 'Successfully logged out.');
    $this->assertFalse(Auth::check());
    $this->assertNull(Session::get('employeeEntries'));
});