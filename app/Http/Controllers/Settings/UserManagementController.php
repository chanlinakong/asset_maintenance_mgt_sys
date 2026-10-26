<?php

namespace App\Http\Controllers\Settings;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query();

        // Search by name or email.
        if ($request->filled('search')) {
            $search = $request->string('search')->trim()->toString();

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role.
        if (
            $request->filled('role') &&
            in_array($request->role, ['admin', 'staff'], true)
        ) {
            $query->where('role', $request->role);
        }

        // Filter by account status.
        if (in_array($request->status, ['active', 'inactive'], true)) {
            $query->where(
                'is_active',
                $request->status === 'active'
            );
        }

        $users = $query
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        if ($request->ajax()) {
            return view('settings.users.partials.table', compact('users'));
        }

        return view('settings.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('settings.users.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::enum(UserRole::class)],
        ]);

        $user = new User();

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = $validated['password'];
        $user->role = UserRole::from($validated['role']);
        $user->is_active = true;

        // The User model's hashed password cast hashes the password.
        $user->save();

        return redirect()
            ->route('settings.users.index')
            ->with('success', 'User account created successfully.');
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', Rule::enum(UserRole::class)],
        ]);

        DB::transaction(function () use ($user, $validated) {
            $target = User::query()
                ->lockForUpdate()
                ->findOrFail($user->id);

            // Prevent accidental removal of your own administrator access.
            if ($target->id === auth()->id()) {
                throw ValidationException::withMessages([
                    'role' => 'You cannot change your own role.',
                ]);
            }

            $newRole = UserRole::from($validated['role']);

            // Protect the last active administrator.
            if (
                $target->role === UserRole::Admin &&
                $newRole !== UserRole::Admin &&
                $target->is_active
            ) {
                $activeAdmins = User::query()
                    ->where('role', UserRole::Admin->value)
                    ->where('is_active', true)
                    ->count();

                if ($activeAdmins <= 1) {
                    throw ValidationException::withMessages([
                        'role' => 'You cannot remove the last active administrator.',
                    ]);
                }
            }

            $target->role = $newRole;
            $target->save();
        });

        return back()->with('success', 'User role updated successfully.');
    }

    public function toggleActive(User $user): RedirectResponse
    {
        DB::transaction(function () use ($user) {
            $target = User::query()
                ->lockForUpdate()
                ->findOrFail($user->id);

            // Prevent an administrator from deactivating themselves.
            if ($target->id === auth()->id()) {
                throw ValidationException::withMessages([
                    'account' => 'You cannot deactivate your own account.',
                ]);
            }

            // Protect the last active administrator.
            if (
                $target->is_active &&
                $target->role === UserRole::Admin
            ) {
                $activeAdmins = User::query()
                    ->where('role', UserRole::Admin->value)
                    ->where('is_active', true)
                    ->count();

                if ($activeAdmins <= 1) {
                    throw ValidationException::withMessages([
                        'account' => 'You cannot deactivate the last active administrator.',
                    ]);
                }
            }

            $target->is_active = !$target->is_active;
            $target->save();
        });

        return back()->with('success', 'Account status updated successfully.');
    }
}
