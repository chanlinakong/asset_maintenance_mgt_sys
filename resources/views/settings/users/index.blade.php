@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl space-y-6 px-4 py-6 sm:px-6 lg:px-8">

    {{-- Page header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                Users & Access
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Manage user accounts, roles, and account access.
            </p>
        </div>

        <a href="{{ route('settings.users.create') }}"
           class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
            <span class="text-lg leading-none">+</span>
            Create User
        </a>
    </div>

    {{-- Success message --}}
    @if (session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
            {{ session('success') }}
        </div>
    @endif

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <p class="font-semibold">Please check the following:</p>
            <ul class="mt-1 list-inside list-disc">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Search and filters --}}
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
        <form method="GET"
              action="{{ route('settings.users.index') }}"
              class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">

            <div class="lg:col-span-1">
                <label for="search" class="mb-1.5 block text-sm font-medium text-gray-700">
                    Search users
                </label>
                <input
                    id="search"
                    name="search"
                    type="search"
                    value="{{ request('search') }}"
                    placeholder="Name or email..."
                    class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
            </div>

            <div>
                <label for="role" class="mb-1.5 block text-sm font-medium text-gray-700">
                    Role
                </label>
                <select
                    id="role"
                    name="role"
                    class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option value="">All roles</option>
                    <option value="admin" @selected(request('role') === 'admin')>Admin</option>
                    <option value="staff" @selected(request('role') === 'staff')>Staff</option>
                </select>
            </div>

            <div>
                <label for="status" class="mb-1.5 block text-sm font-medium text-gray-700">
                    Account status
                </label>
                <select
                    id="status"
                    name="status"
                    class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
                    <option value="">All statuses</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button
                    type="submit"
                    class="flex-1 rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-700"
                >
                    Apply
                </button>

                <a
                    href="{{ route('settings.users.index') }}"
                    class="rounded-lg border border-gray-300 px-4 py-2.5 text-center text-sm font-medium text-gray-700 transition hover:bg-gray-50"
                >
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Users table --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-col gap-1 border-b border-gray-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="font-semibold text-gray-900">User accounts</h2>
            <p class="text-sm text-gray-500">
                {{ $users->total() }} {{ \Illuminate\Support\Str::plural('user', $users->total()) }} found
            </p>
        </div>

        @if ($users->isEmpty())
            <div class="px-6 py-14 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-500">
                    <span class="text-xl">👤</span>
                </div>
                <h3 class="mt-3 text-sm font-semibold text-gray-900">No users found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Try changing your search or filters.
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                User
                            </th>
                            <th scope="col" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Role
                            </th>
                            <th scope="col" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Status
                            </th>
                            <th scope="col" class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Change role
                            </th>
                            <th scope="col" class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                Access
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach ($users as $user)
                            <tr class="transition hover:bg-gray-50">
                                <td class="whitespace-nowrap px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-700">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>

                                        <div>
                                            <p class="font-medium text-gray-900">
                                                {{ $user->name }}
                                                @if ($user->id === auth()->id())
                                                    <span class="ml-1 text-xs font-normal text-gray-500">(You)</span>
                                                @endif
                                            </p>
                                            <p class="mt-0.5 text-sm text-gray-500">
                                                {{ $user->email }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    @if ($user->role === \App\Enums\UserRole::Admin)
                                        <span class="inline-flex rounded-full bg-purple-50 px-2.5 py-1 text-xs font-semibold text-purple-700">
                                            Admin
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">
                                            Staff
                                        </span>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-5 py-4">
                                    @if ($user->is_active)
                                        <span class="inline-flex items-center gap-1.5 text-sm font-medium text-green-700">
                                            <span class="h-2 w-2 rounded-full bg-green-500"></span>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-500">
                                            <span class="h-2 w-2 rounded-full bg-gray-400"></span>
                                            Inactive
                                        </span>
                                    @endif
                                </td>

                                <td class="min-w-56 px-5 py-4">
                                    @if ($user->id === auth()->id())
                                        <span class="text-sm text-gray-400">Your own role</span>
                                    @else
                                        <form method="POST"
                                              action="{{ route('settings.users.update-role', $user) }}"
                                              class="flex items-center gap-2">
                                            @csrf
                                            @method('PATCH')

                                            <select
                                                name="role"
                                                aria-label="Role for {{ $user->name }}"
                                                class="min-w-0 flex-1 rounded-lg border-gray-300 py-1.5 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            >
                                                <option value="admin" @selected($user->role === \App\Enums\UserRole::Admin)>Admin</option>
                                                <option value="staff" @selected($user->role === \App\Enums\UserRole::Staff)>Staff</option>
                                            </select>

                                            <button
                                                type="submit"
                                                class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-semibold text-gray-700 transition hover:bg-gray-100"
                                            >
                                                Save
                                            </button>
                                        </form>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-right">
                                    @if ($user->id === auth()->id())
                                        <span class="text-sm text-gray-400">Current account</span>
                                    @else
                                        <form
                                            method="POST"
                                            action="{{ route('settings.users.toggle-active', $user) }}"
                                            onsubmit="return confirm('Are you sure you want to {{ $user->is_active ? 'deactivate' : 'activate' }} this account?')"
                                            class="inline"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            @if ($user->is_active)
                                                <button
                                                    type="submit"
                                                    class="rounded-lg border border-red-200 px-3 py-2 text-sm font-medium text-red-700 transition hover:bg-red-50"
                                                >
                                                    Deactivate
                                                </button>
                                            @else
                                                <button
                                                    type="submit"
                                                    class="rounded-lg border border-green-200 px-3 py-2 text-sm font-medium text-green-700 transition hover:bg-green-50"
                                                >
                                                    Activate
                                                </button>
                                            @endif
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="border-t border-gray-200 px-4 py-4 sm:px-5">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-gray-500">
                        Showing
                        <span class="font-medium text-gray-700">{{ $users->firstItem() }}</span>
                        to
                        <span class="font-medium text-gray-700">{{ $users->lastItem() }}</span>
                        of
                        <span class="font-medium text-gray-700">{{ $users->total() }}</span>
                    </p>

                    <div>
                        {{ $users->onEachSide(1)->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
