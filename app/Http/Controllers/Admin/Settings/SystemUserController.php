<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\Models\SystemLog;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SystemUserController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'admin')->with('roles')->orderBy('name')->paginate(20);
        return view('admin.settings.users.index', compact('users'));
    }

    public function create()
    {
        $roles   = Role::orderBy('name')->get();
        $members = User::where('role', 'member')->orderBy('name')->get(['id', 'name', 'last_name', 'email']);
        return view('admin.settings.users.create', compact('roles', 'members'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:users,id',
            'password'  => 'required|min:8|confirmed',
            'roles'     => 'nullable|array',
            'roles.*'   => 'exists:roles,id',
        ]);

        $user = User::findOrFail($request->member_id);

        if ($user->role === 'admin') {
            return back()->withErrors(['member_id' => 'This person is already an admin user.']);
        }

        $user->update([
            'role'      => 'admin',
            'password'  => Hash::make($request->password),
            'is_active' => true,
        ]);

        if ($request->roles) {
            $user->roles()->sync($request->roles);
        }

        SystemLog::record('create', 'Settings', "Admin user {$user->email} created.", $user);

        return redirect()->route('admin.settings.users.index')->with('success', 'Admin user created successfully.');
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();
        $permissions = Permission::orderBy('module')->orderBy('name')->get()->groupBy('module');
        $userRoleIds = $user->roles->pluck('id')->toArray();
        $userPerms   = $user->userPermissions()->with('permission')->get()->keyBy('permission_id');

        return view('admin.settings.users.edit', compact('user', 'roles', 'permissions', 'userRoleIds', 'userPerms'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|unique:users,email,' . $user->id,
            'roles'   => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        $user->roles()->sync($request->roles ?? []);

        // Handle individual permission overrides
        if ($request->has('perm_overrides')) {
            UserPermission::where('user_id', $user->id)->delete();
            foreach ($request->perm_overrides as $permId => $type) {
                if (in_array($type, ['grant', 'revoke'])) {
                    UserPermission::create(['user_id' => $user->id, 'permission_id' => $permId, 'type' => $type]);
                }
            }
        }

        SystemLog::record('update', 'Settings', "Admin user {$user->email} updated.", $user);

        return redirect()->route('admin.settings.users.index')->with('success', 'Admin user updated.');
    }

    public function toggleActive(User $user)
    {
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Cannot deactivate the Super Admin.');
        }
        $user->update(['is_active' => !$user->is_active]);
        $action = $user->is_active ? 'activated' : 'deactivated';
        SystemLog::record('update', 'Settings', "Admin user {$user->email} {$action}.", $user);
        return back()->with('success', "User {$action} successfully.");
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate(['password' => 'required|min:8|confirmed']);
        $user->update(['password' => Hash::make($request->password)]);
        SystemLog::record('update', 'Settings', "Password reset for {$user->email}.", $user);
        return back()->with('success', 'Password reset successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Cannot delete the Super Admin.');
        }
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        SystemLog::record('delete', 'Settings', "Admin user {$user->email} deleted.");
        $user->delete();
        return redirect()->route('admin.settings.users.index')->with('success', 'Admin user deleted.');
    }
}
