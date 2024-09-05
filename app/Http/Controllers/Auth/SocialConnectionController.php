<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\SocialConnectionService;

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
    
        // If we have a valid user, proceed with getting employee entries
        $user = $result;
    
        $employeeEntries = $this->socialConnectionService->getEmployeeEntries($user->id);
    
        session(['user_id' => $user->id]);
        session(['employeeEntries' => $employeeEntries]);
    
        return redirect()->route('admin.select.organization')->with(['email' => $user->email]);
    }
    
    
}
