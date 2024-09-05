<?php

use App\Services\SocialConnectionService;
use App\Repositories\SocialRepository;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

beforeEach(function () {
    $this->socialRepository = Mockery::mock(SocialRepository::class);
    $this->socialConnectionService = new SocialConnectionService($this->socialRepository);
});

it('redirects to the social provider', function () {
    $provider = 'google';

    Socialite::shouldReceive('driver')
        ->with($provider)
        ->andReturnSelf();

    Socialite::shouldReceive('redirect')->once();

    $this->socialConnectionService->redirectToProvider($provider);
});

it('handles the callback for social login successfully', function () {
    $provider = 'google';
    $socialUser = new class {
        public $email = 'user@example.com';
        public $id = 'social_id';
        public function getId() {
            return $this->id;
        }
    };
    $user = (object) ['id' => 1];

    Socialite::shouldReceive('driver')
        ->with($provider)
        ->andReturnSelf();
    Socialite::shouldReceive('stateless')->andReturnSelf();
    Socialite::shouldReceive('user')->andReturn($socialUser);

    $this->socialRepository->shouldReceive('findUserByEmail')
        ->with($socialUser->email)
        ->andReturn($user);

    $this->socialRepository->shouldReceive('hasSocialConnection')
        ->with($user->id, $socialUser->getId(), $provider)
        ->andReturn(false);

    $this->socialRepository->shouldReceive('createSocialConnection')
        ->with($user, $socialUser, $provider)
        ->once();

    Auth::shouldReceive('login')->with($user)->once();

    $result = $this->socialConnectionService->handleCallback($provider);

    expect($result)->toBe($user);
});

it('redirects when user is not found', function () {
    $provider = 'google';
    $socialUser = new class {
        public $email = 'user@example.com';
        public $id = 'social_id';
        public function getId() {
            return $this->id;
        }
    };

    Socialite::shouldReceive('driver')
        ->with($provider)
        ->andReturnSelf();
    Socialite::shouldReceive('stateless')->andReturnSelf();
    Socialite::shouldReceive('user')->andReturn($socialUser);

    $this->socialRepository->shouldReceive('findUserByEmail')
        ->with($socialUser->email)
        ->andReturn(null);

    $response = $this->socialConnectionService->handleCallback($provider);

    expect($response)->toBeInstanceOf(Illuminate\Http\RedirectResponse::class);
    expect($response->getSession()->get('errors')->get('email')[0])->toBe('No user found with this email.');
});

it('redirects on error during callback', function () {
    $provider = 'google';

    Socialite::shouldReceive('driver')
        ->with($provider)
        ->andThrow(new Exception('Error during Socialite'));

    $response = $this->socialConnectionService->handleCallback($provider);

    expect($response)->toBeInstanceOf(Illuminate\Http\RedirectResponse::class);
    expect($response->getSession()->get('errors')->get('general')[0])->toBe('An error occurred during login. Please try again.');
});
