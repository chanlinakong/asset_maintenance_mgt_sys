
@php
    $user = auth()->user();
@endphp

<div class="space-y-6">

    <form method="POST"
          action="{{ route('profile.update') }}"
          enctype="multipart/form-data"
          class="space-y-6">

        @csrf
        @method('PATCH')

        {{-- Full Name --}}
        <div>
            <label for="name" class="mb-1.5 block text-sm font-medium text-gray-700">
                Full Name
            </label>

            <input type="text"
                   name="name"
                   id="name"
                   value="{{ old('name', $user->name) }}"
                   required
                   maxlength="255"
                   autocomplete="name"
                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5
                          text-sm focus:border-gray-500 focus:outline-none
                          focus:ring-2 focus:ring-gray-200">

            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700">
                Email Address
            </label>

            <input type="email"
                   name="email"
                   id="email"
                   value="{{ old('email', $user->email) }}"
                   required
                   maxlength="255"
                   autocomplete="email"
                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5
                          text-sm focus:border-gray-500 focus:outline-none
                          focus:ring-2 focus:ring-gray-200">

            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail
                && ! $user->hasVerifiedEmail())

                <p class="mt-2 text-sm text-amber-700">
                    Your email address has not been verified.
                </p>
            @endif
        </div>

        {{-- Role: read-only --}}
        <div>
            <label class="mb-1.5 block text-sm font-medium text-gray-700">
                Account Role
            </label>

            <div class="flex items-center justify-between rounded-lg border border-gray-200 bg-gray-50 px-4 py-3">
                <span class="text-sm font-medium text-gray-700">
                    {{ str($user->role->value)->replace('_', ' ')->title() }}
                </span>

                <span class="text-xs text-gray-500">
                    Managed by administrator
                </span>
            </div>
        </div>

        {{-- Save --}}
        <div class="flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:items-center sm:justify-end">

            <button type="submit"
                    class="inline-flex items-center justify-center rounded-lg
                           bg-gray-900 px-5 py-2.5 text-sm font-semibold
                           text-white transition hover:bg-gray-800
                           focus:outline-none focus:ring-2 focus:ring-gray-400
                           focus:ring-offset-2">
                Save Changes
            </button>

            @if (session('status') === 'profile-updated')
                <p class="text-sm text-green-700">
                    Profile information saved successfully.
                </p>
            @endif

        </div>

    </form>

</div>
