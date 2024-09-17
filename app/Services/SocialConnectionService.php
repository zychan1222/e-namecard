<?php

namespace App\Services;

use Socialite;
use App\Models\User;
use App\Models\UserOrganization;
use App\Repositories\SocialRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SocialConnectionService
{
    protected $socialRepository;

    public function __construct(SocialRepository $socialRepository)
    {
        $this->socialRepository = $socialRepository;
    }

    public function redirectToProvider($provider)
    {
        Log::info('Redirecting to provider.', ['provider' => $provider]);
        return Socialite::driver($provider)->redirect();
    }

    public function handleCallback($provider)
    {
        try {
            $socialUser = $this->getSocialUser($provider);
            Log::info('Social user retrieved from provider.', ['socialUser' => $socialUser]);
    
            $user = $this->socialRepository->findUserByEmail($socialUser->email);
            Log::info('Searching for user by email.', ['email' => $socialUser->email]);
    
            if (!$user) {
                Log::info('User not found. Redirecting to login page.'); 
                return redirect()->route('admin.login.form')->withErrors(['email' => 'No user found with this email.']);
            }
    
            $this->loginUser($user);
            Log::info('User logged in successfully.', ['user_id' => $user->id]);
    
            $this->ensureSocialConnectionExists($user, $socialUser, $provider);
    
            return $user; // Return user if everything is successful
        } catch (\Exception $e) {
            Log::error('Error during social login callback.', ['error' => $e->getMessage()]);
            return redirect()->route('admin.login.form')->withErrors(['general' => 'An error occurred during login. Please try again.']);
        }
    }

    protected function ensureSocialConnectionExists($user, $socialUser, $provider)
    {
        if (!$this->socialRepository->hasSocialConnection($user->id, $socialUser->getId(), $provider)) {
            $this->socialRepository->createSocialConnection($user, $socialUser, $provider);
            Log::info('Social connection created for user.', ['user_id' => $user->id, 'provider' => $provider]);
        } else {
            Log::info('Social connection already exists for user.', ['user_id' => $user->id, 'provider' => $provider]);
        }
    }

    protected function getSocialUser($provider)
    {
        Log::info('Getting social user from provider.', ['provider' => $provider]);
        return Socialite::driver($provider)->stateless()->user();
    }

    public function getUserOrganizations($userId)
    {
        Log::info('Fetching user organizations.', ['user_id' => $userId]);
        return UserOrganization::where('user_id', $userId)
            ->with('organization')
            ->get();
    }

    protected function loginUser($user)
    {
        Log::info('Logging in user.', ['user_id' => $user->id]);
        Auth::login($user);
    }
}
