<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Throwable;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => [
                'nullable',
                'required_without:remove_avatar',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
            'remove_avatar' => [
                'nullable',
                'required_without:avatar',
                'boolean',
            ],
        ]);

        $user = $request->user();
        $oldAvatar = $user->avatar_path;
        $newAvatar = null;
        $removeAvatar = $request->boolean('remove_avatar');

        if (!$removeAvatar && !$request->hasFile('avatar')) {
            throw ValidationException::withMessages([
                'avatar' => 'Please select an image to upload.',
            ]);
        }

        if ($request->hasFile('avatar')) {
            $newAvatar = $request->file('avatar')
                ->store('avatars', 'public');

            if (!$newAvatar) {
                throw ValidationException::withMessages([
                    'avatar' => 'The image could not be uploaded. Please try again.',
                ]);
            }
        }

        try {
            DB::transaction(function () use ($user, $newAvatar, $removeAvatar) {
                $user->avatar_path = $removeAvatar
                    ? null
                    : $newAvatar;

                $user->save();
            });
        } catch (Throwable $exception) {
            if ($newAvatar) {
                Storage::disk('public')->delete($newAvatar);
            }

            throw $exception;
        }

        // Delete the previous file only after the database save succeeds.
        if (
            $oldAvatar
            && ($newAvatar || $removeAvatar)
            && $oldAvatar !== $newAvatar
        ) {
            Storage::disk('public')->delete($oldAvatar);
        }

        return Redirect::route('profile.edit')
            ->with('success', $removeAvatar
                ? 'Profile image removed successfully.'
                : 'Profile image updated successfully.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
