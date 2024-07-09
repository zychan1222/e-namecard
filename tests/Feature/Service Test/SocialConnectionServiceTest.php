<?php

use App\Services\SocialConnectionService;
use App\Repositories\SocialRepository;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;

test('redirects to social provider', function () {
    // Mocking dependencies
    $socialRepository = mock(SocialRepository::class);
    $socialConnectionService = new SocialConnectionService($socialRepository);

    $provider = 'google'; 

    // Expecting Socialite to be called with the correct provider
    Socialite::shouldReceive('driver')->with($provider)->andReturnSelf();
    Socialite::shouldReceive('redirect')->andReturn('mocked_redirect_url');

    // Perform the redirection
    $redirectUrl = $socialConnectionService->redirectToProvider($provider);

    // Assert the returned URL
    expect($redirectUrl)->toBe('mocked_redirect_url');
});

test('mocks socialite user data', function () {
    // Mock Socialite user data
    $socialUser = (object) [
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
    ];

    // Mocking Socialite to return the social user
    Socialite::shouldReceive('driver')->with('google')->andReturnSelf();
    Socialite::shouldReceive('stateless')->andReturnSelf();
    Socialite::shouldReceive('user')->andReturn($socialUser);

    $retrievedUser = Socialite::driver('google')->stateless()->user();
    expect($retrievedUser)->toBe($socialUser);
});

test('finds or creates employee', function () {
    $socialRepository = mock(SocialRepository::class);
    $socialConnectionService = new SocialConnectionService($socialRepository);

    $socialUser = (object) [
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
    ];

    $employee = new \App\Models\Employee();

    // Mocking Socialite to return the social user
    Socialite::shouldReceive('driver')->with('google')->andReturnSelf();
    Socialite::shouldReceive('stateless')->andReturnSelf();
    Socialite::shouldReceive('user')->andReturn($socialUser);

    $socialRepository->shouldReceive('findEmployeeByEmail')->with($socialUser->email)->andReturn(null);
    $socialRepository->shouldReceive('createEmployee')->with([
        'name' => $socialUser->name,
        'email' => $socialUser->email,
        'password' => '',
    ])->andReturn($employee);
    $socialRepository->shouldReceive('createSocialConnection')->with($employee, $socialUser, 'google');

    $returnedEmployee = $socialConnectionService->handleCallback('google');
    expect($returnedEmployee)->toBe($employee);
});


test('creates social connection', function () {
    $socialRepository = mock(SocialRepository::class);
    $socialConnectionService = new SocialConnectionService($socialRepository);

    $socialUser = (object) [
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
    ];

    $employee = new \App\Models\Employee();

    // Mocking Socialite to return the social user
    Socialite::shouldReceive('driver')->with('google')->andReturnSelf();
    Socialite::shouldReceive('stateless')->andReturnSelf();
    Socialite::shouldReceive('user')->andReturn($socialUser);

    $socialRepository->shouldReceive('findEmployeeByEmail')->with($socialUser->email)->andReturn(null);
    $socialRepository->shouldReceive('createEmployee')->with([
        'name' => $socialUser->name,
        'email' => $socialUser->email,
        'password' => '',
    ])->andReturn($employee);
    $socialRepository->shouldReceive('createSocialConnection')->with($employee, $socialUser, 'google');

    $returnedEmployee = $socialConnectionService->handleCallback('google');
    expect($returnedEmployee)->toBe($employee);
});

test('logs in employee', function () {
    $socialRepository = mock(SocialRepository::class);
    $socialConnectionService = new SocialConnectionService($socialRepository);

    $socialUser = (object) [
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
    ];

    $employee = new \App\Models\Employee();
    $employee->is_active = 1;

    // Mocking Socialite to return the social user
    Socialite::shouldReceive('driver')->with('google')->andReturnSelf();
    Socialite::shouldReceive('stateless')->andReturnSelf();
    Socialite::shouldReceive('user')->andReturn($socialUser);

    $socialRepository->shouldReceive('findEmployeeByEmail')->with($socialUser->email)->andReturn($employee);
    $socialRepository->shouldReceive('createEmployee')->andReturn(null);
    $socialRepository->shouldReceive('createSocialConnection')->andReturn(null);

    Auth::shouldReceive('login')->with($employee);

    $returnedEmployee = $socialConnectionService->handleCallback('google');
    expect($returnedEmployee)->toBe($employee);
});

test('throws exception for inactive employee', function () {
    $socialRepository = mock(SocialRepository::class);
    $socialConnectionService = new SocialConnectionService($socialRepository);

    $socialUser = (object) [
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
    ];

    $employee = new \App\Models\Employee();
    $employee->is_active = 0;

    // Mocking Socialite to return the social user
    Socialite::shouldReceive('driver')->with('google')->andReturnSelf();
    Socialite::shouldReceive('stateless')->andReturnSelf();
    Socialite::shouldReceive('user')->andReturn($socialUser);

    $socialRepository->shouldReceive('findEmployeeByEmail')->with($socialUser->email)->andReturn($employee);

    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Your account is inactive.');

    $socialConnectionService->handleCallback('google');
});

