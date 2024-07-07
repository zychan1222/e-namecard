<?php

use App\Models\Employee;
uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('dashboard link', function () {
    $employee = Employee::factory()->create();

    $response = $this->actingAs($employee)->get(route('dashboard'));

    $response->assertStatus(200);
});
test('unauthenticated user cannot view dashboard link', function () {
    $response = $this->get(route('dashboard'));

    $response->assertRedirect(route('login'));
});
test('namecard link', function () {
    $employee = Employee::factory()->create();

    $response = $this->actingAs($employee)->get(route('namecard'));

    $response->assertStatus(200);
});
test('unauthenticated user cannot view namecard link', function () {
    $response = $this->get(route('namecard'));

    $response->assertRedirect(route('login'));
});
test('profile picture retrieved', function () {
    $employee = Employee::factory()->create([
        'profile_pic' => 'profile_picture.jpg',
    ]);

    $response = $this->actingAs($employee)->get(route('dashboard'));

    $response->assertSee($employee->profile_pic);
});
test('default profile picture displayed when null', function () {
    $employee = Employee::factory()->create([
        'profile_pic' => null,
    ]);

    $response = $this->actingAs($employee)->get(route('dashboard'));

    $response->assertSee('default-user.jpg');
});
test('profile view link', function () {
    $employee = Employee::factory()->create();

    $response = $this->actingAs($employee)->get(route('profile.view'));

    $response->assertStatus(200);
});
test('unauthenticated user cannot view profile link', function () {
    $response = $this->get(route('profile.view'));

    $response->assertRedirect(route('login'));
});
test('logout link', function () {
    $employee = Employee::factory()->create();

    $response = $this->actingAs($employee)->post(route('logout'));

    $response->assertRedirect('/');
    $this->assertGuest();
});
