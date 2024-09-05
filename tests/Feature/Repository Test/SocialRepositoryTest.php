<?php
use App\Models\User;
use App\Models\SocialConnection;
use App\Repositories\SocialRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->repository = new SocialRepository();
    $this->user = User::factory()->create();
    $this->socialUser = new class {
        public function getId() {
            return 'social_user_id';
        }
        public $token = 'access_token';
    };
});

it('creates a social connection', function () {
    $provider = 'provider_name';

    $socialConnection = $this->repository->createSocialConnection($this->user, $this->socialUser, $provider);

    expect(SocialConnection::count())->toBe(1);
    expect($socialConnection->user_id)->toBe($this->user->id);
    expect($socialConnection->provider)->toBe($provider);
    expect($socialConnection->provider_id)->toBe('social_user_id');
    expect($socialConnection->access_token)->toBe('access_token');
});

it('checks if a user has a social connection', function () {
    $provider = 'provider_name';

    $this->repository->createSocialConnection($this->user, $this->socialUser, $provider);

    $hasConnection = $this->repository->hasSocialConnection($this->user->id, 'social_user_id', $provider);

    expect($hasConnection)->toBeTrue();
});

it('returns false if a user does not have a social connection', function () {
    $provider = 'provider_name';

    $hasConnection = $this->repository->hasSocialConnection($this->user->id, 'non_existent_user_id', $provider);

    expect($hasConnection)->toBeFalse();
});