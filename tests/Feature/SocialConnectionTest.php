<?php

use App\Models\Employee;
use App\Services\SocialConnectionService;
use App\Repositories\SocialRepository;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    // Mock SocialConnectionService
    $this->socialConnectionService = Mockery::mock(SocialConnectionService::class);
    $this->app->instance(SocialConnectionService::class, $this->socialConnectionService);

    // Mock Socialite behavior for all tests
    Socialite::shouldReceive('driver->stateless->user')->andReturn((object) [
        'id' => '123',
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'token' => 'dummy_token',
    ]);
});

afterEach(function () {
    Mockery::close();
});

test('redirect to google provider', function () {
    $provider = 'google';

    // Mock redirect behavior of SocialConnectionService
    $this->socialConnectionService->shouldReceive('redirectToProvider')->with($provider)->andReturn(
        redirect()->away('https://accounts.google.com/o/oauth2/auth')
    );

    // Make a request to the Google login endpoint
    $response = $this->get("/login/$provider");

    // Assert that the response is a redirect to the Google authentication URL
    $response->assertRedirect('https://accounts.google.com/o/oauth2/auth');
});

test('handle callback', function () {
    $provider = 'google';
    $employee = Employee::factory()->create();

    // Mock handleCallback method of SocialConnectionService
    $this->socialConnectionService->shouldReceive('handleCallback')->with($provider)->andReturn($employee);

    // Request callback endpoint
    $response = $this->get("/login/$provider/callback");

    // Assert redirection to dashboard
    $response->assertRedirect('/dashboard');
});

test('get social user', function () {
    $provider = 'google';

    // Call method getSocialUser from SocialConnectionService
    $socialUser = $this->socialConnectionService->getSocialUser($provider);

    // Assert that returned social user matches mocked user data
    expect($socialUser)->toEqual((object) [
        'id' => '123',
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'token' => 'dummy_token',
    ]);
});

test('handle callback with inactive user', function () {
    $provider = 'google';

    // Create inactive user
    $employee = Employee::factory()->create([
        'email' => 'john@example.com',
        'is_active' => 0,
    ]);

    // Mock SocialRepository to return inactive user
    $socialRepository = Mockery::mock(SocialRepository::class);
    $socialRepository->shouldReceive('findEmployeeByEmail')->with('john@example.com')->andReturn($employee);
    $this->app->instance(SocialRepository::class, $socialRepository);

    // Use mocked SocialRepository in SocialConnectionService constructor
    $this->socialConnectionService = new SocialConnectionService($socialRepository);

    // Expect exception when handling callback for inactive user
    $this->expectException(\Exception::class);
    $this->expectExceptionMessage('Your account is inactive.');

    // Call handleCallback with provider
    $this->socialConnectionService->handleCallback($provider);
});
