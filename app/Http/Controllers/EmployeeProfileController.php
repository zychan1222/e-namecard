<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\TACCode;
use App\Models\UserOrganization;
use App\Models\Role;
use App\Models\RoleHasPermission;

class EmployeeProfileController extends Controller
{
    public function viewEmployeeProfile($employeeId)
    {
        $user = Auth::user();

        try {
            $employee = User::findOrFail($employeeId);

            $userOrg = UserOrganization::where('user_id', $employeeId)
                                       ->where('organization_id', $user->organizations->first()->id)
                                       ->firstOrFail();

            $roleName = $userOrg->organization->owner_id === $employeeId ? 'owner' : (\App\Models\Role::find($userOrg->role_id)->name ?? '');

            if (!$user->organizations->contains($employee->organizations->first())) {
                return redirect()->route('admin.dashboard')->withErrors(['error' => 'Unauthorized access to this employee profile.']);
            }

            return view('admin.employee-profile', [
                'userOrg' => $userOrg,
                'roleName' => $roleName,
                'editMode' => false
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching employee profile', ['employee_id' => $employeeId, 'error' => $e->getMessage()]);
            return redirect()->back()->withErrors(['error' => 'Failed to retrieve employee profile.']);
        }
    }

    public function update(Request $request, $employeeId)
    {
        $user = Auth::user();
    
        // Check if the user has permission to change roles
        $userOrg = UserOrganization::where('user_id', $user->id)
                                   ->where('organization_id', $user->organizations->first()->id)
                                   ->first();
    
        if ($userOrg) {
            $roleId = $userOrg->role_id;
            $permissions = RoleHasPermission::where('role_id', $roleId)->pluck('permission_id')->toArray();
    
            Log::info('User permissions check', ['user_id' => $user->id, 'permissions' => $permissions, 'roles' => [$roleId]]);
    
            if (!in_array(4, $permissions)) {
                return redirect()->back()->withErrors(['error' => 'You do not have permission to change roles.']);
            }
    
            $request->validate([
                'name' => 'required|string|max:255',
                'name_cn' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'department' => 'required|string|max:255',
                'designation' => 'required|string|max:255',
                'is_active' => 'required|boolean',
                'profile_pic' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'role_id' => 'required|in:1,2,3' // Validate the role_id
            ]);
    
            try {
                // Update the employee details
                $employee = User::findOrFail($employeeId);
    
                $employeeOrg = UserOrganization::where('user_id', $employeeId)
                                               ->where('organization_id', $user->organizations->first()->id)
                                               ->first();
    
                if ($employeeOrg && $employeeOrg->role_id == 1) { // Assuming 'owner' role_id is 1
                    return redirect()->back()->withErrors(['error' => 'Owner role cannot be changed.']);
                }
    
                $employee->name = $request->input('name');
                $employee->name_cn = $request->input('name_cn');
                $employee->email = $request->input('email');
                $employee->phone = $request->input('phone');
                $employee->department = $request->input('department');
                $employee->designation = $request->input('designation');
                $employee->is_active = $request->input('is_active');
    
                if ($request->hasFile('profile_pic')) {
                    if ($employee->profile_pic && file_exists(public_path('storage/profile_pics/' . $employee->profile_pic))) {
                        unlink(public_path('storage/profile_pics/' . $employee->profile_pic));
                    }
                    $file = $request->file('profile_pic');
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('storage/profile_pics'), $fileName);
                    $employee->profile_pic = $fileName;
                }
    
                $employee->save();
    
                // Update the role_id if it's not the owner's role
                $roleId = $request->input('role_id');
                if ($employeeOrg && $employeeOrg->role_id != 1) { // Assuming 'owner' role_id is 1
                    UserOrganization::where('user_id', $employeeId)
                                    ->where('organization_id', $user->organizations->first()->id)
                                    ->update(['role_id' => $roleId]);
                }
    
                return redirect()->route('admin.employee.profile', ['employee' => $employeeId])
                                 ->with('success', 'Profile and role updated successfully!');
            } catch (\Exception $e) {
                Log::error('Error updating employee profile', ['employee_id' => $employeeId, 'error' => $e->getMessage()]);
                return redirect()->back()->withErrors(['error' => 'Failed to update profile. Please try again.']);
            }
        } else {
            return redirect()->back()->withErrors(['error' => 'You do not have permission to change roles.']);
        }
    }    

    public function store(Request $request)
    {
        $user = Auth::user();
        $userOrgId = UserOrganization::where('user_id', $user->id)->pluck('organization_id')->first();

        if (!$userOrgId) {
            return redirect()->back()->withErrors(['error' => 'Organization not found for the current user.']);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'name_cn' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'department' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'is_active' => 'required|boolean',
            'profile_pic' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        try {
            DB::beginTransaction();

            $employee = new User();
            $employee->name = $request->input('name');
            $employee->name_cn = $request->input('name_cn');
            $employee->email = $request->input('email');
            $employee->phone = $request->input('phone');
            $employee->department = $request->input('department');
            $employee->designation = $request->input('designation');
            $employee->is_active = $request->input('is_active');

            if ($request->hasFile('profile_pic')) {
                $file = $request->file('profile_pic');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('storage/profile_pics'), $fileName);
                $employee->profile_pic = $fileName;
            }

            $employee->save();

            $tacCode = new TACCode();
            $tacCode->email = $employee->email;
            $tacCode->tac_code = null;
            $tacCode->expires_at = null;
            $tacCode->save();

            $userOrg = new UserOrganization();
            $userOrg->user_id = $employee->id;
            $userOrg->organization_id = $userOrgId;
            $userOrg->role_id = 3;
            $userOrg->save();

            DB::commit();

            return redirect()->route('admin.dashboard')->with('success', 'Employee created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating employee', ['error' => $e->getMessage(), 'data' => $request->all()]);
            return redirect()->back()->withErrors(['error' => 'Failed to create employee. Please try again.']);
        }
    }

    public function destroy($employeeId)
    {
        $user = Auth::user();
    
        // Check if the user has permission to delete users
        $userOrg = UserOrganization::where('user_id', $user->id)
                                   ->where('organization_id', $user->organizations->first()->id)
                                   ->first();
    
        if ($userOrg) {
            $roleId = $userOrg->role_id;
            $permissions = RoleHasPermission::where('role_id', $roleId)->pluck('permission_id')->toArray();
    
            if (!in_array(5, $permissions)) {
                return redirect()->back()->withErrors(['error' => 'You do not have permission to delete users.']);
            }
    
            try {
                $employee = User::findOrFail($employeeId);
                $employeeOrg = UserOrganization::where('user_id', $employeeId)
                                               ->where('organization_id', $user->organizations->first()->id)
                                               ->first();
    
                // Check if the employee is an owner
                if ($employeeOrg && $employeeOrg->role_id == 1) { // Assuming 'owner' role_id is 1
                    return redirect()->back()->withErrors(['error' => 'Owners cannot be deleted.']);
                }
    
                if ($employee->profile_pic && file_exists(public_path('storage/profile_pics/' . $employee->profile_pic))) {
                    unlink(public_path('storage/profile_pics/' . $employee->profile_pic));
                }
    
                UserOrganization::where('user_id', $employeeId)->delete();
                $employee->delete();
    
                return redirect()->route('admin.dashboard')->with('success', 'Employee deleted successfully.');
            } catch (\Exception $e) {
                Log::error('Error deleting employee', ['employee_id' => $employeeId, 'error' => $e->getMessage()]);
                return redirect()->back()->withErrors(['error' => 'Failed to delete employee.']);
            }
        } else {
            return redirect()->back()->withErrors(['error' => 'You do not have permission to delete users.']);
        }
    }    

    public function create()
    {
        // Get the currently authenticated user
        $user = Auth::user();

        // Check if the user is associated with an organization
        $userOrgId = UserOrganization::where('user_id', $user->id)->pluck('organization_id')->first();

        if (!$userOrgId) {
            return redirect()->back()->withErrors(['error' => 'Organization not found for the current user.']);
        }

        // Prepare view data if needed
        $pageTitle = 'Create Employee';

        return view('admin.create-employee', compact('pageTitle'));
    }
}