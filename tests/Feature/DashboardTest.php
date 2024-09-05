<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->organization = Organization::factory()->create();
    $this->employee = Employee::factory()->create([
        'user_id' => $this->user->id,
        'company_id' => $this->organization->id,
    ]);
    $this->actingAs($this->user);
    Session::put('employee_id', $this->employee->id);
});

it('can access the dashboard when authenticated', function () {
    $response = $this->get(route('dashboard'));
    $response->assertStatus(200)->assertViewIs('dashboard');
});

it('redirects to login page on invalid employee', function () {
    Session::put('employee_id', 999);
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

it('can access the dashboard link', function () {
    $response = $this->get(route('dashboard'));
    $response->assertStatus(200);
});

it('can access the namecard link', function () {
    $response = $this->get(route('namecard'));
    $response->assertStatus(200);
});

it('retrieves the profile picture', function () {
    $this->employee->profile_pic = 'profile-pic.jpg';
    $this->employee->save();
    $response = $this->get(route('dashboard'));
    $response->assertSee($this->employee->profile_pic);
});

it('displays default profile picture when null', function () {
    $this->employee->profile_pic = null;
    $this->employee->save();
    $response = $this->get(route('dashboard'));
    $response->assertSee('default-user.jpg');
});

it('can access the profile view link', function () {
    $response = $this->get(route('profile.view'));
    $response->assertStatus(200);
});

it('can logout successfully', function () {
    $response = $this->post(route('logout'));
    $response->assertRedirect('/');
    $this->assertGuest();
});
