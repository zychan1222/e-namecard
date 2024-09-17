<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Auth;
use App\Models\UserOrganization;
use App\Models\Organization;

class ProfileController extends Controller
{
    public function view()
    {
        // Fetch the currently authenticated user
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'User not found.');
        }

        // Get the organization ID from UserOrganization table
        $userOrg = UserOrganization::where('user_id', $user->id)->first();
        $organizationName = '';

        if ($userOrg) {
            // Fetch the organization name
            $organization = Organization::find($userOrg->organization_id);
            $organizationName = $organization ? $organization->name : 'Unknown';
        }

        $pageTitle = 'Profile Page';
        $editMode = false;
        return view('profile', [
            'user' => $user,
            'pageTitle' => $pageTitle,
            'editMode' => $editMode,
            'email' => $user->email,
            'organizationName' => $organizationName,
        ]);
    }
    
    public function edit()
    {
        // Fetch the currently authenticated user
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'User not found.');
        }

        // Get the organization ID from UserOrganization table
        $userOrg = UserOrganization::where('user_id', $user->id)->first();
        $organizationName = '';

        if ($userOrg) {
            // Fetch the organization name
            $organization = Organization::find($userOrg->organization_id);
            $organizationName = $organization ? $organization->name : 'Unknown';
        }

        $pageTitle = 'Edit Profile';
        $editMode = true;
        return view('profile', [
            'user' => $user,
            'pageTitle' => $pageTitle,
            'editMode' => $editMode,
            'email' => $user->email,
            'organizationName' => $organizationName,
        ]);
    }

    public function update(Request $request)
    {
        // Fetch the currently authenticated user
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }
        
        // Validate the profile picture input
        $request->validate([
            'profile_pic' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        
        try {
            // Check if a new profile picture is uploaded
            if ($request->hasFile('profile_pic')) {
                // Delete the old profile picture if it exists
                if ($user->profile_pic && file_exists(public_path('storage/profile_pics/' . $user->profile_pic))) {
                    unlink(public_path('storage/profile_pics/' . $user->profile_pic));
                }
                
                // Save the new profile picture
                $file = $request->file('profile_pic');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('storage/profile_pics'), $fileName);
                $user->profile_pic = $fileName;
            }
            
            // Save the updated user profile
            $user->save();
            
            return redirect()->route('profile.view')->with('success', 'Profile picture updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while updating the profile picture.');
        }
    }
}    