<?php

use App\Models\Employee;
use App\Services\NamecardService;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->namecardService = Mockery::mock(NamecardService::class);
    $this->app->instance(NamecardService::class, $this->namecardService);
});

afterEach(function () {
    Mockery::close();
});

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

    $this->namecardService->shouldReceive('generateVCard')
        ->once()
        ->with($employee->name, $employee->phone)
        ->andReturn('mocked_vcard');
    
    $this->namecardService->shouldReceive('generateQrCode')
        ->once()
        ->with('mocked_vcard')
        ->andReturn('mocked_qr_code');

    $response = $this->get('/namecard');

    $response->assertStatus(200);
    $response->assertViewIs('namecard');
    $response->assertViewHas('employee', $employee);
    $response->assertViewHas('pageTitle', 'Namecard');
    $response->assertViewHas('qrCode', 'mocked_qr_code');
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

    $vCard = "BEGIN:VCARD\nVERSION:3.0\nFN:John Doe\nTEL:1234567890\nEND:VCARD";

    $this->namecardService->shouldReceive('generateVCard')
        ->once()
        ->with($employee->name, $employee->phone)
        ->andReturn($vCard);
    
    $this->namecardService->shouldReceive('generateQrCode')
        ->once()
        ->with($vCard)
        ->andReturn('mocked_qr_code');

    $response = $this->get('/namecard');

    $response->assertStatus(200);
    $response->assertViewHas('qrCode', 'mocked_qr_code');
});

test('generate WhatsApp link', function () {
    $employee = Employee::factory()->create([
        'name' => 'John Doe',
        'phone' => '1234567890',
    ]);

    $this->actingAs($employee);

    $this->namecardService->shouldReceive('generateVCard')
        ->once()
        ->with($employee->name, $employee->phone)
        ->andReturn('mocked_vcard');
    
    $this->namecardService->shouldReceive('generateQrCode')
        ->once()
        ->with('mocked_vcard')
        ->andReturn('mocked_qr_code');

    $expectedUrl = 'whatsapp://send?text=' . urlencode(route('download.vcard.page', ['employee' => $employee]));

    $response = $this->get('/namecard');
    $response->assertStatus(200);
    $response->assertSee($expectedUrl);
});

test('generate Telegram link', function () {
    $employee = Employee::factory()->create([
        'name' => 'John Doe',
        'phone' => '1234567890',
    ]);

    $this->actingAs($employee);

    $this->namecardService->shouldReceive('generateVCard')
        ->once()
        ->with($employee->name, $employee->phone)
        ->andReturn('mocked_vcard');
    
    $this->namecardService->shouldReceive('generateQrCode')
        ->once()
        ->with('mocked_vcard')
        ->andReturn('mocked_qr_code');

    $expectedUrl = 'tg://msg_url?url=' . urlencode(route('download.vcard.page', ['employee' => $employee]));

    $response = $this->get('/namecard');
    $response->assertStatus(200);
    $response->assertSee($expectedUrl);
});

test('capture and save image', function () {
    $employee = Employee::factory()->create([
        'name' => 'John Doe',
        'phone' => '1234567890',
    ]);

    $this->actingAs($employee);

    $this->namecardService->shouldReceive('generateVCard')
        ->once()
        ->with($employee->name, $employee->phone)
        ->andReturn('mocked_vcard');
    
    $this->namecardService->shouldReceive('generateQrCode')
        ->once()
        ->with('mocked_vcard')
        ->andReturn('mocked_qr_code');

    $response = $this->get('/namecard');
    $response->assertStatus(200);

    $response->assertSee('Capture and Save Image');
});

test('accesses download VCard page successfully', function () {
    $employee = Employee::factory()->create();

    $response = $this->actingAs($employee)->get(route('download.vcard.page', ['employee' => $employee]));

    $response->assertStatus(200);
    $response->assertViewIs('vcard_download_page');
});
