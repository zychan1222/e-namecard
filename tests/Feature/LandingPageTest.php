<?php

use App\Models\Employee;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('shows employee details correctly', function () {
    // Create a sample employee
    $employee = Employee::factory()->create([
        'name' => 'Casandra Walsh',
        'name_cn' => 'Miss Cortney Boehm DDS',
        'email' => 'hickle.joe@example.net',
        'phone' => '+1-380-400-2720',
        'designation' => 'Personnel Recruiter',
        'department' => 'ea',
        'company_name' => 'Hirthe Ltd',
    ]);

    // Visit the download VCard page route, assuming authentication middleware is handled
    $response = $this->actingAs($employee)->get(route('download.vcard.page', ['employee' => $employee]));

    // Assert: Check if employee details are displayed correctly
    $response->assertStatus(200)
             ->assertSee($employee->name)
             ->assertSee($employee->name_cn)
             ->assertSee($employee->email)
             ->assertSee($employee->phone)
             ->assertSee($employee->designation)
             ->assertSee($employee->department)
             ->assertSee($employee->company_name);
});

test('displays download button', function () {
    // Create a sample employee
    $employee = Employee::factory()->create();

    // Visit the download VCard page route, assuming authentication middleware is handled
    $response = $this->actingAs($employee)->get(route('download.vcard.page', ['employee' => $employee]));

    // Assert: Check if download button is present
    $response->assertStatus(200)
             ->assertSee('Download VCard');
});

test('explains what is v card', function () {
    // Create a sample employee
    $employee = Employee::factory()->create();

    // Visit the download VCard page route, assuming authentication middleware is handled
    $response = $this->actingAs($employee)->get(route('download.vcard.page', ['employee' => $employee]));

    // Assert: Check if VCard explanation section is present
    $response->assertStatus(200)
             ->assertSee('What is a VCard?')
             ->assertSee('Clicking the Download Button')
             ->assertSee('VCard Format')
             ->assertSee('Saving the VCard')
             ->assertSee('Using the VCard');
});

test('page not accessible when employee is inactive', function () {
    // Create an inactive employee
    $inactiveEmployee = Employee::factory()->create(['is_active' => 0]);

    // Visit the download VCard page route for inactive employee
    $response = $this->actingAs($inactiveEmployee)->get(route('download.vcard.page', ['employee' => $inactiveEmployee]));

    // Assert: Check that page is not accessible
    $response->assertStatus(500); // Adjust status code as per your application's behavior
});
