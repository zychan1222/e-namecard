<?php
namespace App\Services;

use Socialite;
use App\Models\Employee;
use App\Repositories\SocialRepository;
use Illuminate\Support\Facades\Auth;

class SocialConnectionService
{
    protected $socialRepository;

    public function __construct(SocialRepository $socialRepository)
    {
        $this->socialRepository = $socialRepository;
    }

    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleCallback($provider)
    {
        $socialUser = $this->getSocialUser($provider);

        $employee = $this->getEmployee($socialUser, $provider);

        $this->loginEmployee($employee);

        return $employee;
    }

    protected function getSocialUser($provider)
    {
        return Socialite::driver($provider)->stateless()->user();
    }

    protected function getEmployee($socialUser, $provider)
    {
        $employee = $this->socialRepository->findEmployeeByEmail($socialUser->email);

        if (!$employee) {
            $employee = $this->registerEmployee($socialUser, $provider);
        }

        return $employee;
    }

    protected function registerEmployee($socialUser, $provider)
    {
        $employeeData = $this->mapSocialUserToEmployeeData($socialUser);
        $employee = $this->socialRepository->createEmployee($employeeData);

        $this->socialRepository->createSocialConnection($employee, $socialUser, $provider);

        return $employee;
    }

    protected function mapSocialUserToEmployeeData($socialUser)
    {
        return [
            'name' => $socialUser->name,
            'email' => $socialUser->email,
            'password' => '',
        ];
    }

    protected function loginEmployee($employee)
    {
        // Check if the user is active
        if ($employee->is_active != 1) {
            throw new \Exception('Your account is inactive.');
        }

        Auth::login($employee);
    }
}
