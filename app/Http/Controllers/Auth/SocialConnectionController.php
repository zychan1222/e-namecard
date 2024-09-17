<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SocialConnectionService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SocialConnectionController extends Controller
{
    protected $socialConnectionService;

    public function __construct(SocialConnectionService $socialConnectionService)
    {
        $this->socialConnectionService = $socialConnectionService;
    }

    public function redirectToProvider($provider)
    {
        return $this->socialConnectionService->redirectToProvider($provider);
    }

    public function handleCallback($provider)
    {
        $result = $this->socialConnectionService->handleCallback($provider);
    
        if ($result instanceof \Illuminate\Http\RedirectResponse) {
            return $result;
        }
    
        // If we have a valid user, proceed with organization selection
        $user = $result;

        // Fetch user email
        $email = $user->email;
    
        // Fetch organizations associated with the user
        $userOrganizations = $this->socialConnectionService->getUserOrganizations($user->id);
    
        if ($userOrganizations->isEmpty()) {
            // Log and redirect if no organizations are found
            Log::error('No organizations found for user.', ['user_id' => $user->id]);
            return redirect()->route('admin.login.form')->withErrors(['general' => 'No organizations found.']);
        }
    
        return redirect()->route('admin.select.organization', ['email' => $email]);
    }
}
