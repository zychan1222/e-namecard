<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\TACCodeRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TACMail; 
use App\Models\User;
use App\Models\UserOrganization;

class AdminAuthService
{
    protected $userRepo;
    protected $tacRepo;

    public function __construct(UserRepository $userRepo, TACCodeRepository $tacRepo)
    {
        $this->userRepo = $userRepo;
        $this->tacRepo = $tacRepo;
    }

    public function sendTAC(string $email)
    {
        $user = $this->userRepo->findByEmail($email);

        if (!$user) {
            Log::error("No user found with email: $email");
            return false;
        }

        $tacCode = $this->tacRepo->updateOrCreate(
            ['email' => $email],
            ['tac_code' => rand(100000, 999999), 'expires_at' => now()->addMinutes(15)]
        );

        if (!$tacCode) {
            Log::error("Failed to create TAC code for email: $email");
            return false;
        }

        // Assume TACMail is correctly set up
        Mail::to($email)->send(new TACMail($tacCode->tac_code));

        return true;
    }

    public function verifyTAC(string $email, string $tacCode)
    {
        return $this->tacRepo->findValidTAC($email, $tacCode);
    }

    public function getUserOrganizations($emails)
    {
        $userIds = User::whereIn('email', $emails)->pluck('id');

        $userOrganizations = UserOrganization::with('organization') // Eager load organization
            ->whereIn('user_id', $userIds)
            ->get();

        return $userOrganizations;
    }

    public function findUserOrganization(int $userId, int $organizationId)
    {
        return UserOrganization::where('user_id', $userId)
            ->where('organization_id', $organizationId)
            ->first();
    }

    public function loginUser(int $userId)
    {
        $user = $this->userRepo->find($userId);

        if ($user) {
            Auth::login($user);
        }

        return $user;
    }
}
