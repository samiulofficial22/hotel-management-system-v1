<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    protected function guardName(): string
    {
        return config('auth.defaults.guard');
    }

    public function index(): View
    {
        $roles = Role::where('guard_name', $this->guardName())
            ->withCount('users', 'permissions')
            ->orderBy('name')
            ->get();
        return view('roles.index', compact('roles'));
    }

    public function create(): View
    {
        $permissions = Permission::where('guard_name', $this->guardName())->orderBy('name')->get();
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,NULL,id,guard_name,' . $this->guardName()],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role = Role::create([
            'name' => $data['name'],
            'guard_name' => $this->guardName(),
        ]);

        if (! empty($data['permissions'])) {
            $permissionNames = Permission::whereIn('id', $data['permissions'])->pluck('name')->all();
            $role->syncPermissions($permissionNames);
        }

        return redirect()->route('roles.index')->with('success', __('Role created.'));
    }

    public function edit(Role $role): View
    {
        if ($role->guard_name !== $this->guardName()) {
            abort(404);
        }
        $permissions = Permission::where('guard_name', $this->guardName())->orderBy('name')->get();
        $rolePermissionIds = $role->permissions()->pluck('id')->all();
        return view('roles.edit', compact('role', 'permissions', 'rolePermissionIds'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        if ($role->guard_name !== $this->guardName()) {
            abort(404);
        }
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name,' . $role->id . ',id,guard_name,' . $this->guardName()],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role->update(['name' => $data['name']]);

        $permissionIds = $data['permissions'] ?? [];
        $permissionNames = Permission::whereIn('id', $permissionIds)->pluck('name')->all();
        $role->syncPermissions($permissionNames);

        return redirect()->route('roles.index')->with('success', __('Role updated.'));
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->guard_name !== $this->guardName()) {
            abort(404);
        }
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')->with('error', __('Cannot delete role that is assigned to users. Remove the role from all users first.'));
        }
        $role->delete();
        return redirect()->route('roles.index')->with('success', __('Role deleted.'));
    }
}
