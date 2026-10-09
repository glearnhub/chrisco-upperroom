<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SystemLog;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('permissions')->withCount('users')->orderBy('name')->get();
        $permissions = Permission::orderBy('module')->orderBy('name')->get()->groupBy('module');
        return view('admin.settings.roles.index', compact('roles', 'permissions'));
    }

    // ── Roles ──────────────────────────────────────────────────────────────

    public function storeRole(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'slug'        => 'required|string|max:100|unique:roles,slug|alpha_dash',
            'description' => 'nullable|string|max:255',
        ]);

        $role = Role::create($request->only('name', 'slug', 'description'));
        SystemLog::record('create', 'Settings', "Role '{$role->name}' created.", $role);

        return back()->with('success', "Role '{$role->name}' created.");
    }

    public function updateRole(Request $request, Role $role)
    {
        if ($role->is_super_admin) {
            return back()->with('error', 'Cannot modify the Super Admin role.');
        }
        $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update($request->only('name', 'description'));

        // Sync permissions
        $role->permissions()->sync($request->permissions ?? []);
        SystemLog::record('update', 'Settings', "Role '{$role->name}' updated.", $role);

        return back()->with('success', "Role '{$role->name}' updated.");
    }

    public function destroyRole(Role $role)
    {
        if ($role->is_super_admin) {
            return back()->with('error', 'Cannot delete the Super Admin role.');
        }
        SystemLog::record('delete', 'Settings', "Role '{$role->name}' deleted.");
        $role->delete();
        return back()->with('success', 'Role deleted.');
    }

    // ── Permissions ────────────────────────────────────────────────────────

    public function storePermission(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'slug'        => 'required|string|max:100|unique:permissions,slug|alpha_dash',
            'module'      => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
        ]);

        $perm = Permission::create($request->only('name', 'slug', 'module', 'description'));
        SystemLog::record('create', 'Settings', "Permission '{$perm->slug}' created.", $perm);

        return back()->with('success', "Permission '{$perm->name}' created.");
    }

    public function destroyPermission(Permission $permission)
    {
        SystemLog::record('delete', 'Settings', "Permission '{$permission->slug}' deleted.");
        $permission->delete();
        return back()->with('success', 'Permission deleted.');
    }
}
