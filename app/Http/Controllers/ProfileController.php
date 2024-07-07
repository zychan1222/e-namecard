<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use App\Services\ProfileService;
use App\Http\Requests\UpdateProfileRequest;

class ProfileController extends Controller
{
    protected $userService;
    protected $profileService;

    public function __construct(UserService $userService, ProfileService $profileService)
    {
        $this->userService = $userService;
        $this->profileService = $profileService;
    }

    public function view()
    {
        $employee = auth()->user();
        $pageTitle = 'Profile Page';
        $editMode = false;
        return view('profile', compact('employee', 'pageTitle', 'editMode'));
    }
    
    public function edit()
    {
        $employee = auth()->user();
        $pageTitle = 'Edit Profile';
        $editMode = true;
        return view('profile', compact('employee', 'pageTitle', 'editMode'));
    }

    public function update(UpdateProfileRequest $request)
    {
        $user = auth()->user();

        try {
            $data = $this->getValidatedData($request);

            if ($profilePic = $this->profileService->handleProfilePictureUpload($request)) {
                $data['profile_pic'] = $profilePic;
            }

            $this->userService->updateProfile($user, $data);

            return redirect()->back()->with('success', 'Profile updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    protected function getValidatedData(UpdateProfileRequest $request)
    {
        return $request->validated();
    }
}
