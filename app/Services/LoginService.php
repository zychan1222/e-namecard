<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\TACCodeRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\TACMail;
use Illuminate\Support\Facades\Auth;

class LoginService
{
    protected $userRepository;
    protected $tacCodeRepository;

    public function __construct(UserRepository $userRepository, TACCodeRepository $tacCodeRepository)
    {
        $this->userRepository = $userRepository;
        $this->tacCodeRepository = $tacCodeRepository;
    }

    public function sendTAC($email)
    {
        $user = $this->userRepository->findByEmail($email);

        if ($user) {
            $tacCode = $this->tacCodeRepository->updateOrCreate(
                ['email' => $email],
                ['tac_code' => rand(100000, 999999), 'expires_at' => now()->addMinutes(15)]
            );

            Mail::to($email)->send(new TACMail($tacCode->tac_code));

            return true;
        }

        return false;
    }

    public function verifyTAC($email, $tacCode)
    {
        $tac = $this->tacCodeRepository->findValidTAC($email, $tacCode);

        if ($tac) {
            Log::info('TAC Verified Successfully', ['email' => $email]);
            return true;
        }

        Log::error('TAC Verification Failed:', ['email' => $email]);
        return false;
    }

    public function authenticateUser($userId)
    {
        $user = $this->userRepository->find($userId);

        if ($user) {
            Auth::login($user);
            return $user;
        }

        return null;
    }

    public function getUserByEmail($email)
    {
        return $this->userRepository->findByEmail($email);
    }
}
