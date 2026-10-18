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
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
            <div class="flex flex-col gap-6 p-5 sm:p-6 md:flex-row md:items-start">

                {{-- Avatar Form --}}
                <form method="POST" action="{{ route('profile.avatar.update') }}" enctype="multipart/form-data"
                    class="w-full md:max-w-sm">

                    @csrf
                    @method('PATCH')

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

                    <h2 class="text-lg font-semibold text-gray-900">
                        Display Image
                    </h2>

                    <p class="mt-1 text-sm text-gray-500">
                        Preview your image before saving it.
                    </p>

                    <div class="mt-5 flex items-center gap-4">
                        <div class="flex h-24 w-24 shrink-0 items-center justify-center
                                        overflow-hidden rounded-full border border-gray-200
                                        bg-gray-100">

                            <img id="avatar-preview" src="{{ $avatarUrl ?? '' }}" alt="Profile image preview" class="{{ $avatarUrl ? '' : 'hidden' }}
                                           h-full w-full object-cover">

                            <span id="avatar-placeholder" class="{{ $avatarUrl ? 'hidden' : '' }}
                                           flex h-full w-full items-center justify-center
                                           rounded-full bg-blue-100 text-2xl font-bold
                                           text-blue-700">
                                {{ $initials ?: '?' }}
                            </span>
                        </div>

                        <div class="min-w-0 flex-1">
                            <label for="avatar" class="block text-sm font-medium text-gray-700">
                                Choose an image
                            </label>

                            <input id="avatar" name="avatar" type="file"
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="mt-2 block w-full text-sm text-gray-600
                                           file:mr-3 file:rounded-lg file:border-0
                                           file:bg-blue-50 file:px-3 file:py-2
                                           file:font-semibold file:text-blue-700">

                            <p class="mt-1 text-xs text-gray-500">
                                JPG, PNG, WebP · Max 2 MB
                            </p>
                        </div>
                    </div>

                    <p id="avatar-preview-status" class="mt-3 text-sm text-gray-500" aria-live="polite">
                        Your current image is shown above.
                    </p>

                    @error('avatar')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="mt-4 flex flex-wrap items-center gap-3">
                        <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm
                                       font-semibold text-white hover:bg-blue-700">
                            Save Image
                        </button>

                        @if ($user->avatar_path)
                            <button type="submit" form="remove-avatar-form" class="rounded-lg border border-red-200 px-4 py-2
                                               text-sm font-medium text-red-600
                                               hover:bg-red-50">
                                Remove Image
                            </button>
                        @endif
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
    document.addEventListener('DOMContentLoaded', () => {
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