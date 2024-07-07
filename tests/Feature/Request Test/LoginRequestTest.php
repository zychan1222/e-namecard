<?php

use Illuminate\Support\Facades\Validator;
use App\Http\Requests\LoginRequest;


uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

function getValidator(array $data)
{
    $request = new LoginRequest();
    $request->merge($data);
    return Validator::make($data, $request->rules());
}

test('login request validates correctly', function () {
    $validData = [
        'email' => 'user@example.com',
        'password' => 'password123',
    ];

    $validator = getValidator($validData);
    expect($validator->passes())->toBeTrue();
});

test('login request fails without email', function () {
    $invalidData = [
        'password' => 'password123',
    ];

    $validator = getValidator($invalidData);
    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->toArray())->toHaveKey('email');
});

test('login request fails with invalid email', function () {
    $invalidData = [
        'email' => 'invalid-email',
        'password' => 'password123',
    ];

    $validator = getValidator($invalidData);
    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->toArray())->toHaveKey('email');
});

test('login request fails without password', function () {
    $invalidData = [
        'email' => 'user@example.com',
    ];

    $validator = getValidator($invalidData);
    expect($validator->passes())->toBeFalse();
    expect($validator->errors()->toArray())->toHaveKey('password');
});
