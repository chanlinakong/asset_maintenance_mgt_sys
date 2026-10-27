@extends('layouts.app')

@section('title', 'My Account')

@section('content')
    <div class="mx-auto max-w-5xl space-y-6">

        {{-- Page Header --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                My Account
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Manage your personal information and account security.
            </p>
        </div>

        {{-- Success message --}}
        @if (session('success'))
            <div role="status" class="rounded-lg border border-green-200 bg-green-50
                                                                                px-4 py-3 text-sm text-green-800">
                {{ session('success') }}
            </div>
        @endif

        {{-- Validation errors --}}
        @if ($errors->any())
            <div role="alert" class="rounded-lg border border-red-200 bg-red-50
                                                                                px-4 py-3 text-sm text-red-700">
                <p class="font-semibold">
                    Please correct the following errors:
                </p>

                <ul class="mt-2 list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Profile Overview --}}
        @php
            $initials = collect(explode(' ', trim($user->name)))
                ->filter()
                ->take(2)
                ->map(fn($part) => mb_strtoupper(mb_substr($part, 0, 1)))
                ->implode('');

            $avatarUrl = $user->avatar_path
                ? asset('storage/' . $user->avatar_path)
                : null;
        @endphp

        <div class="overflow-hidden rounded-xl border border-gray-200
                        bg-white shadow-sm">

            <div class="flex flex-col gap-6 p-5 sm:p-6 md:flex-row md:items-start">
                {{-- Avatar Form --}}
                <form id="avatar-form" method="POST" action="{{ route('profile.avatar.update') }}"
                    enctype="multipart/form-data" class="w-full min-w-0 md:max-w-sm">

                    @csrf
                    @method('PATCH')

                    <div class="flex flex-col gap-7 sm:flex-row sm:items-start">

                        {{-- Avatar Preview --}}
                        <div class="flex shrink-0 flex-col items-center">
                            <div class="relative">
                                <div class="flex h-28 w-28 items-center justify-center
                                                overflow-hidden rounded-full border-4
                                                border-white bg-blue-50 shadow-md
                                                ring-1 ring-gray-200 sm:h-32 sm:w-32">

                                    <img id="avatar-preview" src="{{ $avatarUrl ?? '' }}" alt="Profile photo preview" class="{{ $avatarUrl ? '' : 'hidden' }}
                                                   h-full w-full object-cover">

                                    <span id="avatar-placeholder" class="{{ $avatarUrl ? 'hidden' : '' }}
                                                   flex h-full w-full items-center
                                                   justify-center rounded-full
                                                   bg-gradient-to-br from-blue-100 to-indigo-100
                                                   text-3xl font-bold tracking-wide
                                                   text-blue-700">
                                        {{ $initials ?: '?' }}
                                    </span>
                                </div>

                                <label for="avatar" class="absolute bottom-1 right-1 flex h-9 w-9
                                               cursor-pointer items-center justify-center
                                               rounded-full border-2 border-white
                                               bg-blue-600 text-white shadow-sm
                                               transition hover:bg-blue-700" title="Change profile photo">

                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M4 20h4l10.5-10.5
                                                   a2.121 2.121 0 00-3-3L5 17v3z" />
                                    </svg>
                                </label>
                            </div>

                            <span class="mt-3 text-xs font-medium text-gray-500">
                                Your Profile
                            </span>
                        </div>

                        {{-- Upload Area --}}
                        <div class="min-w-0 flex-1">

                            <div class="rounded-xl border border-dashed
                                            border-gray-300 bg-gray-50/70 p-5
                                            transition hover:border-blue-400
                                            hover:bg-blue-50/40 sm:p-6">

                                <div class="flex flex-col items-center text-center">

                                    <div class="flex h-12 w-12 items-center
                                                    justify-center rounded-xl
                                                    bg-white text-blue-600 shadow-sm
                                                    ring-1 ring-gray-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V4m0 0L7 9m5-5l5 5M5 16v3
                                                       a1 1 0 001 1h12a1 1 0 001-1v-3" />
                                        </svg>
                                    </div>

                                    <p class="mt-4 text-sm font-semibold text-gray-900">
                                        Choose a profile photo
                                    </p>

                                    <label for="avatar" class="mt-4 inline-flex cursor-pointer
                                                   items-center justify-center gap-2
                                                   rounded-lg border border-gray-300
                                                   bg-white px-4 py-2.5 text-sm
                                                   font-semibold text-gray-700 shadow-sm
                                                   transition hover:bg-gray-50
                                                   focus-within:ring-2
                                                   focus-within:ring-blue-500
                                                   focus-within:ring-offset-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Browse files
                                    </label>

                                    <input id="avatar" name="avatar" type="file"
                                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="sr-only"
                                        aria-describedby="avatar-file-info avatar-preview-status">

                                    <!-- <p id="avatar-file-info" class="mt-3 text-xs text-gray-500">
                                        JPG, PNG or WebP <span class="mx-1">·</span>
                                        Maximum 2 MB
                                    </p> -->
                                </div>
                            </div>

                            @error('avatar')
                                <p class="mt-2 text-sm font-medium text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                            {{-- Actions --}}
                            <div class="mt-6 flex items-center gap-3 justify-end">

                                <button id="avatar-save-button" type="submit" class="inline-flex items-center justify-center
                                               gap-2 rounded-lg bg-blue-600 px-5 py-2.5
                                               text-sm font-semibold text-white shadow-sm
                                               transition hover:bg-blue-700
                                               focus:outline-none focus:ring-2
                                               focus:ring-blue-500 focus:ring-offset-2
                                               disabled:cursor-not-allowed
                                               disabled:opacity-50">

                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-6" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>

                                    Save
                                </button>

                                @if ($user->avatar_path)
                                    <button type="submit" form="remove-avatar-form" class="inline-flex items-center justify-center
                                                                       gap-2 rounded-lg border border-gray-200
                                                                       bg-white px-4 py-2.5 text-sm
                                                                       font-medium text-gray-600
                                                                       transition hover:border-red-200
                                                                       hover:bg-red-50 hover:text-red-600">

                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12m-10 0V5a1 1 0
                                                                           011-1h4a1 1 0 011 1v2m2
                                                                           0l-.8 13H8.8L8 7m3 3v6m2-6v6" />
                                        </svg>

                                        Remove
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>

                {{-- User Information --}}
                <div class="min-w-0 flex-1 border-t border-gray-100 pt-5
                                md:border-l md:border-t-0 md:pl-6 md:pt-0">

                    <h2 class="break-words text-lg font-semibold text-gray-900">
                        {{ $user->name }}
                    </h2>

                    <p class="mt-1 break-all text-sm text-gray-500">
                        {{ $user->email }}
                    </p>

                    <div class="mt-3">
                        <span class="inline-flex rounded-full bg-blue-50 px-3 py-1
                                         text-xs font-semibold text-blue-700">
                            {{ str($user->role?->value ?? $user->role ?? 'user')
        ->replace('_', ' ')
        ->title() }}
                        </span>
                    </div>

                    <p class="mt-4 text-sm text-gray-500">
                        Update your name, email, and password using the sections below.
                    </p>
                </div>

            </div>
        </div>

        {{-- Separate form for safe image removal --}}
        @if ($user->avatar_path)
            <form id="remove-avatar-form" method="POST" action="{{ route('profile.avatar.update') }}" class="hidden">
                @csrf
                @method('PATCH')
                <input type="hidden" name="remove_avatar" value="1">
            </form>
        @endif

        {{-- Success Message --}}
        @if (session('status'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                @if (session('status') === 'profile-updated')
                    Profile information updated successfully.
                @elseif (session('status') === 'password-updated')
                    Password updated successfully.
                @elseif (session('status') === 'verification-link-sent')
                    A new email verification link has been sent.
                @else
                    {{ session('status') }}
                @endif
            </div>
        @endif

        {{-- Error Message --}}
        @if (session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>
        @endif

        {{-- Profile Information --}}
        <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-5 py-5 sm:px-6">
                <h2 class="font-semibold text-gray-900">
                    Personal Information
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Update your name and email address.
                </p>
            </div>

            <div class="p-5 sm:p-6">
                @include('profile.partials.update-profile-information-form')
            </div>
        </section>

        {{-- Password & Security --}}
        <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-5 py-5 sm:px-6">
                <h2 class="font-semibold text-gray-900">
                    Password & Security
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Keep your account secure by using a strong password.
                </p>
            </div>

            <div class="p-5 sm:p-6">
                @include('profile.partials.update-password-form')
            </div>
        </section>

        {{-- Danger Zone --}}
        <section class="overflow-hidden rounded-xl border border-red-200 bg-white shadow-sm">
            <div class="border-b border-red-100 bg-red-50/50 px-5 py-5 sm:px-6">
                <h2 class="font-semibold text-red-700">
                    Delete Account
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    Permanently delete your account and associated account data.
                    This action cannot be easily undone.
                </p>
            </div>

            <div class="p-5 sm:p-6">
                @include('profile.partials.delete-user-form')
            </div>
        </section>

    </div>
@endsection
{{-- Live image preview --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('avatar');
        const preview = document.getElementById('avatar-preview');
        const placeholder = document.getElementById('avatar-placeholder');
        const removeCheckbox = document.getElementById('remove-avatar');
        const status = document.getElementById('avatar-preview-status');

        if (!input || !preview || !placeholder) return;

        const originalImage = @json($avatarUrl);
        let previewUrl = null;

        function showOriginalImage() {
            if (originalImage) {
                preview.src = originalImage;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            } else {
                preview.removeAttribute('src');
                preview.classList.add('hidden');
                placeholder.classList.remove('hidden');
            }
        }

        function releasePreviewUrl() {
            if (previewUrl) {
                URL.revokeObjectURL(previewUrl);
                previewUrl = null;
            }
        }

        input.addEventListener('change', () => {
            const file = input.files?.[0];

            if (!file) {
                releasePreviewUrl();
                showOriginalImage();

                if (status) {
                    status.textContent =
                        'Your current image is displayed above.';
                }

                return;
            }

            const allowedTypes = [
                'image/jpeg',
                'image/png',
                'image/webp',
            ];

            if (!allowedTypes.includes(file.type)) {
                input.value = '';
                releasePreviewUrl();
                showOriginalImage();

                if (status) {
                    status.textContent =
                        'Choose a JPG, PNG, or WebP image.';
                }

                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                input.value = '';
                releasePreviewUrl();
                showOriginalImage();

                if (status) {
                    status.textContent =
                        'The image must not exceed 2 MB.';
                }

                return;
            }

            releasePreviewUrl();
            previewUrl = URL.createObjectURL(file);
            preview.src = previewUrl;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');

            if (removeCheckbox) {
                removeCheckbox.checked = false;
            }

            if (status) {
                status.textContent =
                    'Preview only — click Save Changes to apply it.';
            }
        });

        if (removeCheckbox) {
            removeCheckbox.addEventListener('change', () => {
                if (removeCheckbox.checked) {
                    input.value = '';
                    releasePreviewUrl();
                    showOriginalImage();

                    preview.classList.add('hidden');
                    placeholder.classList.remove('hidden');

                    if (status) {
                        status.textContent =
                            'The image will be removed when you save.';
                    }
                } else {
                    showOriginalImage();

                    if (status) {
                        status.textContent =
                            'Your current image is displayed above.';
                    }
                }
            });
        }

        window.addEventListener('pagehide', releasePreviewUrl);
    });
</script>