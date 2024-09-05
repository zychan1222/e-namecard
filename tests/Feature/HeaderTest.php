<?php
use App\Models\Employee;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->organization = Organization::factory()->create();
    $this->employee = Employee::factory()->create([
        'user_id' => $this->user->id,
        'company_id' => $this->organization->id,
    ]);

    // Authenticate the user
    $this->actingAs($this->user);
    Session::put('employee_id', $this->employee->id);
});

test('dashboard link', function () {
    $response = $this->get(route('dashboard'));
    $response->assertStatus(200);
});

test('namecard link', function () {
    $response = $this->get(route('namecard'));
    $response->assertStatus(200);
});

test('profile picture retrieved', function () {
    $this->employee->update(['profile_pic' => 'profile_picture.jpg']);
    $response = $this->get(route('dashboard'));
    $response->assertSee($this->employee->profile_pic);
});

test('default profile picture displayed when null', function () {
    $this->employee->update(['profile_pic' => null]);
    $response = $this->get(route('dashboard'));
    $response->assertSee('default-user.jpg');
});

test('profile view link', function () {
    $response = $this->get(route('profile.view'));
    $response->assertStatus(200);
});

test('logout link', function () {
    $response = $this->post(route('logout'));
    $response->assertRedirect('/');
    $this->assertGuest();
});

