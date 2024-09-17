<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserOrganization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function showAdminDashboard(Request $request)
    {
        $userOrganization = $this->getUserOrganization(Auth::id());

        if (!$userOrganization) {
            return redirect()->route('admin.select.organization')
                ->withErrors(['organization_id' => 'You are not associated with any organization.']);
        }

        $organizationId = $userOrganization->organization_id;
        $admins = $this->getUsersByRole($organizationId, [1, 2]);
        $normalUsers = $this->getUsersByRole($organizationId, [3]);

        $userOrganizations = $this->getFilteredAndSortedUsers($request, $organizationId);

        $organization = $userOrganization->organization;
        $searchMessage = $request->input('search') ? 'Showing results for: ' . $request->input('search') : '';
        $currentQueryParams = $request->except('page');

        return view('admin.dashboard', compact(
            'admins', 
            'normalUsers', 
            'userOrganizations', 
            'organization', 
            'searchMessage', 
            'currentQueryParams'
        ));
    }

    public function updateRoles(Request $request)
    {
        $this->validateRoleUpdates($request);

        $this->applyRoleUpdates($request->input('role_updates'));

        return redirect()->route('admin.dashboard')->with('success', 'Roles updated successfully.');
    }

    protected function getUserOrganization($userId)
    {
        return UserOrganization::where('user_id', $userId)->first();
    }

    protected function getUsersByRole($organizationId, array $roleIds)
    {
        return UserOrganization::where('organization_id', $organizationId)
            ->whereIn('role_id', $roleIds)
            ->with('user')
            ->get();
    }

    protected function getFilteredAndSortedUsers(Request $request, $organizationId)
    {
        $query = UserOrganization::where('organization_id', $organizationId)
            ->with('user')
            ->join('users', 'user_organization.user_id', '=', 'users.id')
            ->select('user_organization.*', 'users.name', 'users.email');

        $this->applySearchFilter($query, $request->input('search'));
        $this->applySortFilter($query, $request->input('sort-by'));
        $this->applyUserTypeFilter($query, $request->input('filter-user'));

        return $query->paginate(10);
    }

    protected function applySearchFilter($query, $searchTerm)
    {
        if ($searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('users.name', 'like', "%{$searchTerm}%")
                  ->orWhere('users.email', 'like', "%{$searchTerm}%");
            });
        }
    }

    protected function applySortFilter($query, $sortBy)
    {
        switch ($sortBy) {
            case 'name_asc':
                $query->orderBy('users.name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('users.name', 'desc');
                break;
            case 'email_asc':
                $query->orderBy('users.email', 'asc');
                break;
            case 'email_desc':
                $query->orderBy('users.email', 'desc');
                break;
            default:
                $query->orderBy('users.name', 'asc');
                break;
        }
    }

    protected function applyUserTypeFilter($query, $filterUser)
    {
        if ($filterUser === 'admins_only') {
            $query->whereIn('role_id', [1, 2]);
        } elseif ($filterUser === 'non_admins') {
            $query->where('role_id', 3);
        }
    }

    protected function validateRoleUpdates(Request $request)
    {
        $request->validate([
            'role_updates' => 'required|array',
            'role_updates.*.user_id' => 'required|exists:users,id',
            'role_updates.*.role_id' => 'required|in:1,2,3'
        ]);
    }

    protected function applyRoleUpdates(array $roleUpdates)
    {
        foreach ($roleUpdates as $update) {
            UserOrganization::where('user_id', $update['user_id'])
                ->where('organization_id', Auth::user()->organization_id)
                ->update(['role_id' => $update['role_id']]);
        }
    }
}