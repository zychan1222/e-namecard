<?php

use Illuminate\Support\Facades\Validator;
use App\Http\Requests\RegisterRequest;
use App\Models\Employee;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

/**
 * Get an instance of the validator for a given data set.
 *
 * @param array $data
 * @return \Illuminate\Contracts\Validation\Validator
 */
function getCustomValidator(array $data)
{
    $request = new RegisterRequest();
    $request->merge($data);
    return Validator::make($data, $request->rules());
}

test('register request validates correctly', function () {
    $validData = [
        'name' => 'John Doe',
        'email' => 'user@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $validator = getCustomValidator($validData);
    expect($validator->passes())->toBeTrue();
});

test('register request fails without name', function () {
    $invalidData = [
        'email' => 'user@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $validator = getCustomValidator($invalidData);
    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->toArray())->toHaveKey('name');
});

test('register request fails without email', function () {
    $invalidData = [
        'name' => 'John Doe',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $validator = getCustomValidator($invalidData);
    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->toArray())->toHaveKey('email');
});

test('register request fails with invalid email', function () {
    $invalidData = [
        'name' => 'John Doe',
        'email' => 'invalid-email',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $validator = getCustomValidator($invalidData);
    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->toArray())->toHaveKey('email');
});

test('register request fails without password', function () {
    $invalidData = [
        'name' => 'John Doe',
        'email' => 'user@example.com',
    ];

    $validator = getCustomValidator($invalidData);
    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->toArray())->toHaveKey('password');
});

test('register request fails when password confirmation does not match', function () {
    $invalidData = [
        'name' => 'John Doe',
        'email' => 'user@example.com',
        'password' => 'password123',
        'password_confirmation' => 'differentpassword',
    ];

    $validator = getCustomValidator($invalidData);
    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->toArray())->toHaveKey('password');
});

test('register request fails when email is not unique', function () {
    $existingEmployee = Employee::factory()->create([
        'email' => 'user@example.com',
    ]);

    $invalidData = [
        'name' => 'John Doe',
        'email' => 'user@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $validator = getCustomValidator($invalidData);
    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->toArray())->toHaveKey('email');
});
