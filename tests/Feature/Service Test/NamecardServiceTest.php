<?php

use App\Services\NamecardService;
use App\Models\Employee;
use Illuminate\Support\Facades\Artisan;
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
    $employee = Employee::factory()->create([
        'name' => 'John Doe',
        'phone' => '1234567890',
    ]);

    $this->actingAs($employee);

    $response = $this->get('/namecard');

    $response->assertStatus(200);

    $vCard = "BEGIN:VCARD\nVERSION:3.0\nFN:John Doe\nTEL:1234567890\nEND:VCARD";

    $qrCode = \QrCode::size(100)->generate($vCard);

    $response->assertViewHas('qrCode', $qrCode);
});