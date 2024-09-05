<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Services\OrganizationService;
use App\Http\Requests\UpdateOrganizationRequest;

class OrganizationController extends Controller
{
    protected $organizationService;

    public function __construct(OrganizationService $organizationService)
    {
        $this->organizationService = $organizationService;
    }

    public function manageOrganization(Request $request)
    {
        $adminId = $request->session()->get('admin_id');
        $admin = Admin::find($adminId);

        if (!$admin) {
            return redirect()->route('admin.dashboard')->withErrors(['error' => 'Admin not found.']);
        }

        $employee = $admin->employee;

        if (!$employee) {
            return redirect()->route('admin.dashboard')->withErrors(['error' => 'Employee not found.']);
        }

        $organization = $employee->organization()->with('owner')->first();

        if (!$organization) {
            return redirect()->route('admin.dashboard')->withErrors(['error' => 'Organization not found.']);
        }

        return view('admin.organization', [
            'editMode' => false,
            'organization' => $organization,
        ]);
    }

    public function update(UpdateOrganizationRequest $request, $organizationId)
    {
        $validatedData = $request->validated();

        try {
            $this->organizationService->updateOrganization($organizationId, $validatedData);
            return redirect()->route('admin.organization')
                ->with('success', 'Organization updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'An error occurred while updating the organization.']);
        }
    }
}
