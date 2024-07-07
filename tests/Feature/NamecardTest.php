<?php

use App\Models\Employee;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('redirects to login if user is not authenticated', function () {
    $response = $this->get('/namecard');

    $response->assertRedirect('/login');
});

test('redirects to profile view if profile is incomplete', function () {
    $employee = Employee::factory()->create([
        'name' => 'John Doe',
        'phone' => null,
    ]);

    $this->actingAs($employee);

    $response = $this->get('/namecard');

    $response->assertRedirect('/profile');
    $response->assertSessionHas('error', 'Please complete your profile details to generate a QR code.');
});

test('displays namecard if user is authenticated and profile is complete', function () {
    $employee = Employee::factory()->create([
        'name' => 'John Doe',
        'phone' => '1234567890',
        'designation' => 'Software Engineer',
        'department' => 'IT',
        'company_name' => 'Tech Company',
        'profile_pic' => null, 
    ]);

    $this->actingAs($employee);

    $response = $this->get('/namecard');

    $response->assertStatus(200);
    $response->assertViewIs('namecard');
    $response->assertViewHas('employee', $employee);
    $response->assertViewHas('pageTitle', 'Namecard');
    $response->assertViewHas('qrCode');
});

test('generates QR code with correct VCard data', function () {
    $employee = Employee::factory()->create([
        'name' => 'John Doe',
        'phone' => '1234567890',
        'designation' => 'Software Engineer',
        'department' => 'IT',
        'company_name' => 'Tech Company',
        'profile_pic' => null,
    ]);

    $this->actingAs($employee);

    $response = $this->get('/namecard');

    $response->assertStatus(200);

    $vCard = "BEGIN:VCARD\nVERSION:3.0\nFN:John Doe\nTEL:1234567890\nEND:VCARD";

    $qrCode = \QrCode::size(100)->generate($vCard);

    $response->assertViewHas('qrCode', $qrCode);
});

test('generate WhatsApp link', function () {
    $employee = Employee::factory()->create();
    $expectedUrl = 'whatsapp://send?text=' . urlencode(route('download.vcard.page', ['employee' => $employee]));

    $response = $this->actingAs($employee)->get('/namecard');
    $response->assertStatus(200);
    $response->assertSee($expectedUrl);
});

test('generate Telegram link', function () {
    $employee = Employee::factory()->create();
    $expectedUrl = 'tg://msg_url?url=' . urlencode(route('download.vcard.page', ['employee' => $employee]));

    $response = $this->actingAs($employee)->get('/namecard');
    $response->assertStatus(200);
    $response->assertSee($expectedUrl);
});

test('capture and save image', function () {
    $employee = Employee::factory()->create();

    $response = $this->actingAs($employee)->get('/namecard');
    $response->assertStatus(200);

    $response->assertSee('Capture and Save Image');
});

test('accesses download VCard page successfully', function () {
    $employee = Employee::factory()->create();

    $response = $this->actingAs($employee)->get(route('download.vcard.page', ['employee' => $employee]));

    $response->assertStatus(200);
    $response->assertViewIs('vcard_download_page');
});
