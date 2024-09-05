<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Services\TACService;

class UserService
{
    protected $userRepository;
    protected $tacService;

    public function __construct(UserRepository $userRepository, TACService $tacService)
    {
        $this->userRepository = $userRepository;
        $this->tacService = $tacService;
    }

    public function generateAndSendTAC($email)
    {
        $user = $this->userRepository->findByEmail($email);
        if ($user) {
            $tacCode = $this->tacService->generateTAC();
            $this->tacService->sendTAC($user, $tacCode);
            return $user;
        }
        return null;
    }
    
    public function registerEmail($email)
    {
        $user = $this->userRepository->findByEmail($email);

        if ($user) {
            $this->sendTAC($user);
        } else {
            $user = $this->createUserAndSendTAC($email);
        }

        return $user;
    }

    public function findByEmail($email)
    {
        return $this->userRepository->findByEmail($email);
    }
    
    protected function sendTAC($user)
    {
        $tacCode = $this->tacService->generateTAC();
        $this->tacService->sendTAC($user, $tacCode);
    }

    protected function createUserAndSendTAC($email)
    {
        $user = $this->userRepository->create(['email' => $email]);
        $this->sendTAC($user);
        return $user;
    }

    public function verifyTAC($email, $tac)
    {
        $user = $this->userRepository->findByEmail($email);
        return $user && $user->tac_code === $tac && now()->lessThanOrEqualTo($user->tac_expiry);
    }
}