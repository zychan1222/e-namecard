<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Repositories\TACCodeRepository;
use App\Repositories\OrganizationRepository;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\TACMail;

class RegisterService
{
    protected $userRepo;
    protected $tacRepo;
    protected $orgRepo;

    public function __construct(UserRepository $userRepo, TACCodeRepository $tacRepo, OrganizationRepository $orgRepo)
    {
        $this->userRepo = $userRepo;
        $this->tacRepo = $tacRepo;
        $this->orgRepo = $orgRepo;
    }

    public function registerEmail($email, $name)
    {
        $user = $this->userRepo->create([
            'email' => $email,
            'name' => $name,
            'password' => Hash::make('temporary-password'),
        ]);

        $tacCode = $this->tacRepo->updateOrCreate(
            ['email' => $email],
            ['tac_code' => rand(100000, 999999), 'expires_at' => now()->addMinutes(15)]
        );

        Mail::to($email)->send(new TACMail($tacCode->tac_code));

        return $user;
    }

    public function verifyTAC($email, $tac)
    {
        return $this->tacRepo->findValidTAC($email, $tac);
    }

    public function registerOrganization($data, $user)
    {
        $organization = $this->orgRepo->create($data);
        $user->organizations()->attach($organization->id, ['role_id' => Role::where('name', 'owner')->first()->id]);
        return $organization;
    }

    public function getUserRepository()
    {
        return $this->userRepo;
    }
}
