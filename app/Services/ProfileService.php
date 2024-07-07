<?php
namespace App\Services;

use Illuminate\Http\Request;

class ProfileService
{
    public function handleProfilePictureUpload(Request $request)
    {
        if ($request->hasFile('profile_pic')) {
            $profilePicPath = $request->file('profile_pic')->store('profile_pics', 'public');
            return basename($profilePicPath);
        }
        return null;
    }
}
?>