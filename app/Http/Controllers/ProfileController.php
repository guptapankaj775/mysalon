<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    private function authorizeAdmin()
    {
        if (\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->role === 'admin') {
            abort(403, 'Profile settings are not available for admin users.');
        }
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $this->authorizeAdmin();
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $this->authorizeAdmin();
        $user = Auth::user();
        $validated = $request->validated();
        $validated['has_no_gst'] = $request->boolean('has_no_gst');

        if ($validated['has_no_gst']) {
            $validated['gst_number'] = null;
        }

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if (!empty($validated['password'])) {
            $user->password = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }

        $slugChanged = $user->isDirty('slug') || $user->isDirty('salon_name');
        $user->save();

        if (!empty($validated['password'])) {
            if ($slugChanged && $user->slug) {
                $refererPath = $request->headers->get('referer') ? parse_url($request->headers->get('referer'), PHP_URL_PATH) : null;
                if ($refererPath === '/profile') {
                    return Redirect::route('profile.edit')
                        ->with('status', 'profile-updated')
                        ->with('password-status', 'password-updated');
                }
                return Redirect::route('salon.dashboard', ['salon' => $user->slug])
                    ->with('status', 'profile-updated')
                    ->with('password-status', 'password-updated');
            }
            return Redirect::back()->with('status', 'profile-updated')->with('password-status', 'password-updated');
        }

        if ($slugChanged && $user->slug) {
            $refererPath = $request->headers->get('referer') ? parse_url($request->headers->get('referer'), PHP_URL_PATH) : null;
            if ($refererPath === '/profile') {
                return Redirect::route('profile.edit')->with('status', 'profile-updated');
            }
            return Redirect::route('salon.dashboard', ['salon' => $user->slug])->with('status', 'profile-updated');
        }

        return Redirect::back()->with('status', 'profile-updated');
    }

    /**
     * Update the user's profile photo.
     */
    public function updatePhoto(Request $request): \Illuminate\Http\RedirectResponse
    {
        $this->authorizeAdmin();
        $request->validate([
            'profile_photo' => ['required', 'image', 'max:1024'], // max 1MB
        ]);

        // Delete old photo if exists
        if ($request->user()->profile_photo) {
            Storage::disk('public')->delete($request->user()->profile_photo);
        }

        $path = $request->file('profile_photo')->store('profile-photos', 'public');

        $request->user()->update([
            'profile_photo' => $path
        ]);

        return back()->with('success', 'Profile photo updated successfully');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();
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
