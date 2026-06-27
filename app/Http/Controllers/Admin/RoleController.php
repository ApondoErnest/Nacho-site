<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RoleUpdateRequest;
use App\Models\Role;
use App\Support\AdminAccess;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class RoleController extends Controller
{
    public function index(): View
    {
        return view('admin.roles.index', [
            'roles' => $this->roles(),
            'abilityMatrix' => AdminAccess::matrix(),
        ]);
    }

    public function show(Role $role): View
    {
        $role->loadCount('users');

        return view('admin.roles.show', [
            'role' => $role,
            'abilities' => AdminAccess::abilitiesForRole($role->slug),
        ]);
    }

    public function edit(Role $role): View
    {
        return view('admin.roles.edit', [
            'role' => $role,
            'abilities' => AdminAccess::abilitiesForRole($role->slug),
        ]);
    }

    public function update(RoleUpdateRequest $request, Role $role): RedirectResponse
    {
        $role->update($request->roleAttributes());

        return redirect()
            ->route('admin.roles.show', $role)
            ->with('status', 'Role updated.');
    }

    private function roles()
    {
        return Role::query()
            ->whereIn('slug', array_keys(AdminAccess::matrix()))
            ->withCount('users')
            ->orderByRaw("CASE slug WHEN 'super-admin' THEN 1 WHEN 'admin' THEN 2 WHEN 'center-manager' THEN 3 WHEN 'receptionist' THEN 4 WHEN 'inspector' THEN 5 WHEN 'content-manager' THEN 6 ELSE 99 END")
            ->orderBy('name')
            ->get();
    }
}
