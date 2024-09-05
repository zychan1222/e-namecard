<?php

use App\Services\NamecardService;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Tests\TestCase;

test('generates vCard correctly', function () {
    $service = new NamecardService();

    $name = 'John Doe';
    $phone = '1234567890';

    $expectedVCard = "BEGIN:VCARD\nVERSION:3.0\nFN:{$name}\nTEL:{$phone}\nEND:VCARD";

    $generatedVCard = $service->generateVCard($name, $phone);

    expect($generatedVCard)->toBe($expectedVCard);
});

test('generates QR code properly', function () {
    $user = User::factory()->create();
    $employee = Employee::factory()->create([
        'user_id' => $user->id,
        'name' => 'John Doe',
        'phone' => '1234567890',
    ]);

    $this->actingAs($user);
    Session::put('employee_id', $employee->id);

    $this->mock(NamecardService::class, function ($mock) use ($employee) {
        $mock->shouldReceive('generateVCard')
            ->once()
            ->with($employee->name, $employee->phone)
            ->andReturn("BEGIN:VCARD\nVERSION:3.0\nFN:John Doe\nTEL:1234567890\nEND:VCARD");

        $mock->shouldReceive('generateQrCode')
            ->once()
            ->with("BEGIN:VCARD\nVERSION:3.0\nFN:John Doe\nTEL:1234567890\nEND:VCARD")
            ->andReturn('mocked_qr_code');
    });

    $response = $this->get('/namecard');

    $response->assertStatus(200);

    $vCard = "BEGIN:VCARD\nVERSION:3.0\nFN:John Doe\nTEL:1234567890\nEND:VCARD";

    $qrCode = \QrCode::size(100)->generate($vCard);

    $response->assertViewHas('qrCode', 'mocked_qr_code');
});
