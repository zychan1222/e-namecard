<?php

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use App\Models\Employee;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;


uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->employee = Employee::factory()->create();
});

function extractErrorsFromResponse($response)
{
    $errors = json_decode($response->getContent(), true)['errors'] ?? [];

    return Arr::flatten($errors);
}

test('profile pic must be an image file', function () {
    $response = $this->actingAs($this->employee)->json('PUT', route('profile.update'), [
        'profile_pic' => 'not-an-image.txt',
    ]);

    $errors = extractErrorsFromResponse($response);

    expect(in_array('The profile picture must be an image file.', $errors))->toBeTrue();
});

test('profile pic must have allowed file types', function () {
    $response = $this->actingAs($this->employee)->json('PUT', route('profile.update'), [
        'profile_pic' => UploadedFile::fake()->create('document.pdf'),
    ]);

    $errors = extractErrorsFromResponse($response);

    expect(in_array('The profile picture must be a JPEG, PNG, JPG, GIF, or SVG file.', $errors))->toBeTrue();
});

test('profile pic cannot exceed maximum file size', function () {
    $response = $this->actingAs($this->employee)->json('PUT', route('profile.update'), [
        'profile_pic' => UploadedFile::fake()->image('profile.jpg')->size(3000),
    ]);

    $errors = extractErrorsFromResponse($response);

    expect(in_array('The profile picture may not be greater than 2048 kilobytes in size.', $errors))->toBeTrue();
});

test('name cannot exceed maximum length', function () {
    $response = $this->actingAs($this->employee)->json('PUT', route('profile.update'), [
        'name' => str_repeat('A', 256),
    ]);

    $errors = extractErrorsFromResponse($response);

    expect(in_array('The name may not be greater than 255 characters.', $errors))->toBeTrue();
});

test('name cn cannot exceed maximum length', function () {
    $response = $this->actingAs($this->employee)->json('PUT', route('profile.update'), [
        'name_cn' => str_repeat('汉', 256),
    ]);

    $errors = extractErrorsFromResponse($response);

    expect(in_array('The Chinese name may not be greater than 255 characters.', $errors))->toBeTrue();
});

test('phone cannot exceed maximum length', function () {
    $response = $this->actingAs($this->employee)->json('PUT', route('profile.update'), [
        'phone' => str_repeat('1', 21),
    ]);

    $errors = extractErrorsFromResponse($response);

    expect(in_array('The phone may not be greater than 20 characters.', $errors))->toBeTrue();
});

test('company name cannot exceed maximum length', function () {
    $response = $this->actingAs($this->employee)->json('PUT', route('profile.update'), [
        'company_name' => str_repeat('Company Name', 30),
    ]);

    $errors = extractErrorsFromResponse($response);

    expect(in_array('The company name may not be greater than 255 characters.', $errors))->toBeTrue();
});

test('department cannot exceed maximum length', function () {
    $response = $this->actingAs($this->employee)->json('PUT', route('profile.update'), [
        'department' => str_repeat('Department', 30),
    ]);

    $errors = extractErrorsFromResponse($response);

    expect(in_array('The department may not be greater than 255 characters.', $errors))->toBeTrue();
});

test('designation cannot exceed maximum length', function () {
    $response = $this->actingAs($this->employee)->json('PUT', route('profile.update'), [
        'designation' => str_repeat('Designation', 30),
    ]);

    $errors = extractErrorsFromResponse($response);

    expect(in_array('The designation may not be greater than 255 characters.', $errors))->toBeTrue();
});
