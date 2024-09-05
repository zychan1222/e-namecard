<?php
use App\Repositories\UserRepository;
use App\Services\TACService;
use App\Services\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;

beforeEach(function () {
    $this->userRepository = mock(UserRepository::class);
    $this->tacService = mock(TACService::class);
    $this->userService = new UserService($this->userRepository, $this->tacService);
});

it('generates and sends TAC for existing user', function () {
    $email = 'user@example.com';
    $user = (object)['email' => $email];

    $this->userRepository->shouldReceive('findByEmail')->with($email)->andReturn($user);
    $this->tacService->shouldReceive('generateTAC')->andReturn('123456');
    $this->tacService->shouldReceive('sendTAC')->with($user, '123456');

    $result = $this->userService->generateAndSendTAC($email);

    expect($result)->toBe($user);
});

it('returns null when user does not exist for TAC generation', function () {
    $email = 'nonexistent@example.com';

    $this->userRepository->shouldReceive('findByEmail')->with($email)->andReturn(null);

    $result = $this->userService->generateAndSendTAC($email);

    expect($result)->toBeNull();
});

it('registers email and sends TAC if user does not exist', function () {
    $email = 'newuser@example.com';
    $user = (object)['email' => $email];

    $this->userRepository->shouldReceive('findByEmail')->with($email)->andReturn(null);
    $this->userRepository->shouldReceive('create')->with(['email' => $email])->andReturn($user);
    $this->tacService->shouldReceive('generateTAC')->andReturn('654321');
    $this->tacService->shouldReceive('sendTAC')->with($user, '654321');

    $result = $this->userService->registerEmail($email);

    expect($result)->toBe($user);
});

it('registers email and sends TAC if user already exists', function () {
    $email = 'existinguser@example.com';
    $user = (object)['email' => $email];

    $this->userRepository->shouldReceive('findByEmail')->with($email)->andReturn($user);
    $this->tacService->shouldReceive('generateTAC')->andReturn('789012');
    $this->tacService->shouldReceive('sendTAC')->with($user, '789012');

    $result = $this->userService->registerEmail($email);

    expect($result)->toBe($user);
});

it('finds user by email', function () {
    $email = 'user@example.com';
    $user = (object)['email' => $email];

    $this->userRepository->shouldReceive('findByEmail')->with($email)->andReturn($user);

    $result = $this->userService->findByEmail($email);

    expect($result)->toBe($user);
});

it('returns null if user not found by email', function () {
    $email = 'nonexistent@example.com';

    $this->userRepository->shouldReceive('findByEmail')->with($email)->andReturn(null);

    $result = $this->userService->findByEmail($email);

    expect($result)->toBeNull();
});

it('verifies TAC correctly when valid', function () {
    $email = 'user@example.com';
    $tac = '123456';
    $user = (object)[
        'email' => $email,
        'tac_code' => $tac,
        'tac_expiry' => now()->addMinutes(10),
    ];

    $this->userRepository->shouldReceive('findByEmail')->with($email)->andReturn($user);

    $result = $this->userService->verifyTAC($email, $tac);

    expect($result)->toBeTrue();
});

it('does not verify TAC if TAC does not match', function () {
    $email = 'user@example.com';
    $tac = 'wrong_tac';
    $user = (object)[
        'email' => $email,
        'tac_code' => '123456',
        'tac_expiry' => now()->addMinutes(10),
    ];

    $this->userRepository->shouldReceive('findByEmail')->with($email)->andReturn($user);

    $result = $this->userService->verifyTAC($email, $tac);

    expect($result)->toBeFalse();
});

it('does not verify TAC if expired', function () {
    $email = 'user@example.com';
    $tac = '123456';
    $user = (object)[
        'email' => $email,
        'tac_code' => $tac,
        'tac_expiry' => now()->subMinutes(10),
    ];

    $this->userRepository->shouldReceive('findByEmail')->with($email)->andReturn($user);

    $result = $this->userService->verifyTAC($email, $tac);

    expect($result)->toBeFalse();
});
