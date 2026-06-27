<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserStoreRequest;
use App\Http\Requests\Admin\UserUpdateRequest;
use App\Models\Role;
use App\Models\User;
use App\Support\AdminAccess;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search'));
        $status = trim((string) $request->query('status'));
        $roleId = trim((string) $request->query('role_id'));

        $users = User::query()
            ->with('role')
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            }))
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($roleId !== '', fn ($query) => $query->where('role_id', $roleId))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'roles' => $this->roles(),
            'statuses' => UserStatus::cases(),
            'search' => $search,
            'status' => $status,
            'roleId' => $roleId,
            'counts' => $this->counts(),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'staffUser' => new User([
                'status' => UserStatus::ACTIVE,
            ]),
            'roles' => $this->roles(),
            'statuses' => UserStatus::cases(),
        ]);
    }

    public function store(UserStoreRequest $request): RedirectResponse
    {
        $user = User::query()->create([
            ...$request->userAttributes(),
            'email_verified_at' => now(),
        ]);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('status', 'Staff user created.');
    }

    public function show(User $user): View
    {
        $user->load('role');

        return view('admin.users.show', [
            'staffUser' => $user,
        ]);
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'staffUser' => $user->load('role'),
            'roles' => $this->roles(),
            'statuses' => UserStatus::cases(),
        ]);
    }

    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        $request->ensureCurrentUserKeepsAccess();

        $user->update($request->userAttributes());

        return redirect()
            ->route('admin.users.show', $user)
            ->with('status', 'Staff user updated.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()?->is($user)) {
            throw ValidationException::withMessages([
                'user' => 'You cannot deactivate your own account.',
            ]);
        }

        $user->update([
            'status' => UserStatus::INACTIVE,
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Staff user deactivated.');
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

    /**
     * @return array<string, int>
     */
    private function counts(): array
    {
        return [
            'total' => User::query()->count(),
            'active' => User::query()->where('status', UserStatus::ACTIVE->value)->count(),
            'inactive' => User::query()->where('status', UserStatus::INACTIVE->value)->count(),
            'super_admin' => User::query()
                ->whereHas('role', fn ($query) => $query->where('slug', 'super-admin'))
                ->count(),
        ];
    }
}
