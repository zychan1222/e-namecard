<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Services\NamecardService;
use App\Models\User;
use App\Models\UserOrganization;
use App\Models\Organization;

class NamecardController extends Controller
{
    protected $namecardService;

    public function __construct(NamecardService $namecardService)
    {
        $this->namecardService = $namecardService;
    }

    public function showNamecard()
    {
        // Get the authenticated user
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'User not found.');
        }

        // Get the user's organization details
        $userOrg = UserOrganization::where('user_id', $user->id)->first();

        if (!$userOrg) {
            return redirect()->back()->with('error', 'User organization not found.');
        }

        $organization = Organization::find($userOrg->organization_id);
        $organizationName = $organization ? $organization->name : 'Unknown';

        // Email is directly from the User model
        $email = $user->email ?? 'Email not available';

        // Generate vCard and QR Code for the user
        $pageTitle = 'Namecard';
        $vCard = $this->namecardService->generateVCard($user->name, $user->phone);
        $qrCode = $this->namecardService->generateQrCode($vCard);

        return view('namecard', compact('user', 'userOrg', 'email', 'pageTitle', 'qrCode', 'organizationName'));
    }

    public function downloadVCard($name, $phone)
    {
        $vCardContent = $this->namecardService->generateVCard($name, $phone);
        $filename = $name . '.vcf';

        return response($vCardContent, 200)
            ->header('Content-Type', 'text/x-vcard')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function showVCardDownloadPage()
    {
        // Get the authenticated user
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'User not found.');
        }

        // Get the user's organization details
        $userOrg = UserOrganization::where('user_id', $user->id)->first();

        if (!$userOrg) {
            return redirect()->back()->with('error', 'User organization not found.');
        }

        // Check if the user is active
        if ($user->is_active != 1) {
            abort(403, 'The account is inactive.');
        }

        // Email is directly from the User model
        $email = $user->email ?? 'Email not available';

        return view('vcard_download_page', compact('user', 'userOrg', 'email'));
    }
}
