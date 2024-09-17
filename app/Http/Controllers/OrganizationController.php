<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Organization;
use App\Http\Requests\UpdateOrganizationRequest;
use App\Services\OrganizationService;

class OrganizationController extends Controller
{
    protected $organizationService;

    public function __construct(OrganizationService $organizationService)
    {
        $this->organizationService = $organizationService;
    }

    public function manageOrganization()
    {
        $user = Auth::user();

        $userOrganization = $this->organizationService->getUserOrganization($user->id);
        
        if (!$userOrganization || !$userOrganization->organization) {
            return redirect()->route('admin.dashboard')->withErrors(['error' => 'Organization not found.']);
        }

        $organization = $userOrganization->organization;
        $ownerEmail = $this->organizationService->getOwnerEmail($organization->id);

        return view('admin.organization', [
            'editMode' => false,
            'organization' => $organization,
            'ownerEmail' => $ownerEmail,
        ]);
    }

    public function update(UpdateOrganizationRequest $request, $organizationId)
    {
        $validatedData = $request->validated();
        $organization = Organization::findOrFail($organizationId);

        if ($request->hasFile('logo')) {
            $validatedData['logo'] = $this->organizationService->handleLogoUpload($request->file('logo'), $organization->logo);
        }

        try {
            $this->organizationService->updateOrganization($organization, $validatedData);
            return redirect()->route('admin.organization')
                ->with('success', 'Organization updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'An error occurred while updating the organization.']);
        }
    }
}