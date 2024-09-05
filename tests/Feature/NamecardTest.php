<?php

use App\Models\Employee;
use App\Models\User;
use App\Models\Organization;
use App\Services\NamecardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;

uses(RefreshDatabase::class);

it('displays namecard if user is authenticated and profile is complete', function () {
    $user = User::factory()->create();
    $employee = Employee::factory()->create([
        'user_id' => $user->id,
        'name' => 'John Doe',
        'phone' => '1234567890',
        'designation' => 'Software Engineer',
        'department' => 'IT',
        'company_id' => 3,
        'profile_pic' => null,
    ]);

    $this->actingAs($user);
    Session::put('employee_id', $employee->id);

    // Mock the NamecardService
    $this->mock(NamecardService::class, function ($mock) use ($employee) {
        $mock->shouldReceive('generateVCard')
            ->once()
            ->with($employee->name, $employee->phone)
            ->andReturn('mocked_vcard');

        $mock->shouldReceive('generateQrCode')
            ->once()
            ->with('mocked_vcard')
            ->andReturn('mocked_qr_code');
    });

    $response = $this->get('/namecard');

    $response->assertStatus(200)
             ->assertViewIs('namecard')
             ->assertViewHas('employee', $employee)
             ->assertViewHas('pageTitle', 'Namecard')
             ->assertViewHas('qrCode', 'mocked_qr_code');
});

it('redirects back if employee not found', function () {
    $user = User::factory()->create();
    Session::put('employee_id', 999); // Non-existent employee_id

    $this->actingAs($user);

    $response = $this->get('/namecard');

    $response->assertRedirect()
             ->assertSessionHas('error', 'Employee not found.');
});

it('downloads vcard', function () {
    $user = User::factory()->create();
    $employee = Employee::factory()->create(['user_id' => $user->id, 'name' => 'John Doe', 'phone' => '1234567890']);

    $this->actingAs($user);
    Session::put('employee_id', $employee->id);

    $this->mock(NamecardService::class, function ($mock) {
        $mock->shouldReceive('generateVCard')
            ->with('John Doe', '1234567890')
            ->andReturn("BEGIN:VCARD\nVERSION:3.0\nFN:John Doe\nTEL:1234567890\nEND:VCARD");
    });

    $response = $this->get('/download-vcard/John%20Doe/1234567890');

    $response->assertStatus(200)
             ->assertHeader('Content-Type', 'text/x-vcard; charset=UTF-8')
             ->assertHeader('Content-Disposition', 'attachment; filename="John Doe.vcf"')
             ->assertSee("BEGIN:VCARD\nVERSION:3.0\nFN:John Doe\nTEL:1234567890\nEND:VCARD");
});

it('shows vcard download page', function () {
    $user = User::factory()->create();
    $employee = Employee::factory()->create(['user_id' => $user->id, 'is_active' => 1]);
    Session::put('employee_id', $employee->id);

    $this->actingAs($user);

    $response = $this->get(route('download.vcard.page', ['employee' => $employee]));

    $response->assertStatus(200)
             ->assertViewIs('vcard_download_page')
             ->assertViewHas('employee', $employee);
});

it('throws exception if employee is inactive', function () {
    $user = User::factory()->create();
    $employee = Employee::factory()->create(['user_id' => $user->id, 'is_active' => 0]);
    Session::put('employee_id', $employee->id);

    $this->actingAs($user);

    $this->get(route('download.vcard.page', ['employee' => $employee]))
        ->assertStatus(403);
});