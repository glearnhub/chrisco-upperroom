<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\Models\SystemLog;
use App\Models\User;
use App\Mail\AdminWelcomeMail;
use App\Models\UserPermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
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
            'roles'     => 'nullable|array',
            'roles.*'   => 'exists:roles,id',
        ]);

        $user = User::findOrFail($request->member_id);

        if ($user->role === 'admin') {
            return back()->withErrors(['member_id' => 'This person is already an admin user.']);
        }

        $user->forceFill([
            'role'                 => 'admin',
            'password'             => Hash::make(Str::random(32)),
            'is_active'            => true,
            'must_change_password' => true,
        ])->save();

        if ($request->roles) {
            $user->roles()->sync($request->roles);
        }

        $token    = Password::createToken($user);
        $resetUrl = url(route('password.reset', ['token' => $token, 'email' => $user->email], false));

        try {
            Mail::to($user->email)->send(new AdminWelcomeMail($user, $resetUrl));
        } catch (\Exception $e) {
            \Log::error("Welcome email failed for {$user->email}: " . $e->getMessage());
        }

        SystemLog::record('create', 'Settings', "Admin user {$user->email} created.", $user);

        return redirect()->route('admin.settings.users.index')->with('success', 'Admin user created successfully. A password-set link has been sent to their email.');
    }

    public function edit(User $user)
    {
        abort_if($user->role !== 'admin', 404);
        $roles = Role::orderBy('name')->get();
        $permissions = Permission::orderBy('module')->orderBy('name')->get()->groupBy('module');
        $userRoleIds = $user->roles->pluck('id')->toArray();
        $userPerms   = $user->userPermissions()->with('permission')->get()->keyBy('permission_id');

        return view('admin.settings.users.edit', compact('user', 'roles', 'permissions', 'userRoleIds', 'userPerms'));
    }

    public function update(Request $request, User $user)
    {
        abort_if($user->role !== 'admin', 404);
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
            $overrides = $request->perm_overrides;
            if (is_array($overrides)) {
                $permIds = array_map('intval', array_keys($overrides));
                $validIds = Permission::whereIn('id', $permIds)->pluck('id')->flip();

                UserPermission::where('user_id', $user->id)->delete();
                foreach ($overrides as $permId => $type) {
                    $permId = (int) $permId;
                    if (in_array($type, ['grant', 'revoke']) && $validIds->has($permId)) {
                        UserPermission::create(['user_id' => $user->id, 'permission_id' => $permId, 'type' => $type]);
                    }
                }
            }
        }

        SystemLog::record('update', 'Settings', "Admin user {$user->email} updated.", $user);

        return redirect()->route('admin.settings.users.index')->with('success', 'Admin user updated.');
    }

    public function toggleActive(User $user)
    {
        abort_if($user->role !== 'admin', 404);
        if ($user->isSuperAdmin()) {
            return back()->with('error', 'Cannot deactivate the Super Admin.');
        }
        $newState = !$user->is_active;
        $user->forceFill(['is_active' => $newState])->save();
        $action = $newState ? 'activated' : 'deactivated';
        SystemLog::record('update', 'Settings', "Admin user {$user->email} {$action}.", $user);
        return back()->with('success', "User {$action} successfully.");
    }

    public function resetPassword(Request $request, User $user)
    {
        abort_if($user->role !== 'admin', 404);
        $user->update([
            'password'             => Hash::make(Str::random(32)),
            'must_change_password' => true,
        ]);

        $token    = Password::createToken($user);
        $resetUrl = url(route('password.reset', ['token' => $token, 'email' => $user->email], false));

        try {
            Mail::to($user->email)->send(new AdminWelcomeMail($user, $resetUrl));
        } catch (\Exception $e) {
            \Log::error("Password reset email failed for {$user->email}: " . $e->getMessage());
        }

        SystemLog::record('update', 'Settings', "Password reset for {$user->email}.", $user);
        return back()->with('success', 'Password reset successfully. A new password-set link has been sent to their email.');
    }

    public function destroy(User $user)
    {
        abort_if($user->role !== 'admin', 404);
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
