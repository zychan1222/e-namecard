<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\SocialConnection;
use Illuminate\Support\Facades\Log;

class SocialRepository
{
    public function findUserByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    public function createUser(array $userData)
    {
        return User::create($userData);
    }

    public function createSocialConnection($user, $socialUser, $provider)
    {
        Log::info('Creating social connection.', [
            'user_id' => $user->id,
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
            'access_token' => $socialUser->token,
        ]);
    
        $socialConnection = SocialConnection::create([
            'user_id' => $user->id,
            'provider' => $provider,
            'provider_id' => $socialUser->getId(),
            'access_token' => $socialUser->token,
        ]);
    
        Log::info('Social connection created.', ['social_connection' => $socialConnection]);
        return $socialConnection;
    }

    public function hasSocialConnection($userId, $providerId, $provider)
    {
        return SocialConnection::where('user_id', $userId)
            ->where('provider_id', $providerId)
            ->where('provider', $provider)
            ->exists();
    }
}
