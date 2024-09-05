<?php

use App\Repositories\UserRepository;
use App\Models\User;

beforeEach(function () {
    $this->userRepository = new UserRepository();
});

it('can find a user by email', function () {
    $user = User::factory()->create(['email' => 'test@example.com']);
    
    $foundUser = $this->userRepository->findByEmail('test@example.com');
    
    expect($foundUser)->toBeInstanceOf(User::class);
    expect($foundUser->email)->toBe('test@example.com');
});

it('returns null when no user is found by email', function () {
    $foundUser = $this->userRepository->findByEmail('nonexistent@example.com');
    
    expect($foundUser)->toBeNull();
});

it('can find a user by id', function () {
    $user = User::factory()->create();
    
    $foundUser = $this->userRepository->findById($user->id);
    
    expect($foundUser)->toBeInstanceOf(User::class);
    expect($foundUser->id)->toBe($user->id);
});

it('returns null when no user is found by id', function () {
    $foundUser = $this->userRepository->findById(999);
    
    expect($foundUser)->toBeNull();
});

it('can create a user', function () {
    $userData = [
        'email' => 'testuser@example.com',
    ];
    
    $createdUser = $this->userRepository->create($userData);
    
    expect($createdUser)->toBeInstanceOf(User::class);
    expect($createdUser->email)->toBe($userData['email']);
});
