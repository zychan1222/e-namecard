<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SocialConnectionService;

class SocialConnectionController extends Controller
{
    protected $SocialConnectionService;

    public function __construct(SocialConnectionService $SocialConnectionService)
    {
        $this->SocialConnectionService = $SocialConnectionService;
    }

    public function redirectToProvider($provider)
    {
        return $this->SocialConnectionService->redirectToProvider($provider);
    }

    public function handleCallback($provider)
    {
        $this->SocialConnectionService->handleCallback($provider);

        return redirect()->intended('/dashboard');
    }
}
