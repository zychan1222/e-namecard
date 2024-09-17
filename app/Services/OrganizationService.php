<?php
namespace App\Services;

use App\Models\User;
use App\Models\Organization;
use App\Models\UserOrganization;
use Illuminate\Support\Facades\Storage;

class OrganizationService
{
    public function getUserOrganization($userId)
    {
        return UserOrganization::where('user_id', $userId)
            ->with('organization')
            ->first();
    }

    public function getOwnerEmail($organizationId)
    {
        // Find the user ID for the owner (role_id = 1) in the UserOrganization table
        $userId = UserOrganization::where('organization_id', $organizationId)
            ->where('role_id', 1)
            ->value('user_id');
    
        // Fetch the email from the User table using the user ID
        $email = $userId ? User::find($userId)->email : 'No email found';
    
        return $email;
    }    

    public function handleLogoUpload($logo, $oldLogo = null)
    {
        $logoFileName = $logo->getClientOriginalName();
        $logoPath = 'storage/logo';

        // Store the new logo
        $logo->storeAs($logoPath, $logoFileName);

        // Delete the old logo if it exists
        if ($oldLogo && Storage::exists($logoPath.'/'.$oldLogo)) {
            Storage::delete($logoPath.'/'.$oldLogo);
        }

        return $logoFileName;
    }

    public function updateOrganization($organization, $data)
    {
        $organization->update($data);
    }
}
