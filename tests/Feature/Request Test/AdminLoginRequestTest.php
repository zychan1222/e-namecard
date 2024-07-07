<?php

use Illuminate\Support\Facades\Validator;
use App\Http\Requests\AdminLoginRequest;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

/**
 * Get an instance of the validator for admin login.
 *
 * @param array $data
 * @return \Illuminate\Contracts\Validation\Validator
 */
function adminValidator(array $data)
{
    $request = new AdminLoginRequest();
    $request->merge($data);
    return Validator::make($data, $request->rules());
}

test('admin login request validates correctly', function () {
    $validData = [
        'email' => 'admin@example.com',
        'password' => 'password123',
    ];

    $validator = adminValidator($validData);
    expect($validator->passes())->toBeTrue();
});

test('admin login request fails without email', function () {
    $invalidData = [
        'password' => 'password123',
    ];

    $validator = adminValidator($invalidData);
    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->toArray())->toHaveKey('email');
});

test('admin login request fails with invalid email', function () {
    $invalidData = [
        'email' => 'invalid-email',
        'password' => 'password123',
    ];

    $validator = adminValidator($invalidData);
    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->toArray())->toHaveKey('email');
});

test('admin login request fails without password', function () {
    $invalidData = [
        'email' => 'admin@example.com',
    ];

    $validator = adminValidator($invalidData);
    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->toArray())->toHaveKey('password');
});
