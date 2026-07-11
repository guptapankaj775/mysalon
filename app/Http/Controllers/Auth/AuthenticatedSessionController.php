<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = Auth::user();

        // Redirect non-admins without active plan (or whose owner doesn't have an active plan) to subscription selection
        $owner = $user->created_by ? \App\Models\User::find($user->created_by) : $user;
        if (!$user->isAdmin() && (!$owner || !$owner->hasActivePlan())) {
            if ($user->slug) {
                return redirect()->route('salon.subscription.index', ['salon' => $user->slug])
                    ->with('info', 'Welcome! Please select a subscription plan to get started.');
            }
            return redirect()->route('subscription.index')
                ->with('info', 'Welcome! Please select a subscription plan to get started.');
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $salon = $request->attributes->get('salon') ?? $request->route('salon');
        $slug = is_string($salon) ? $salon : ($salon?->slug ?? null);

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($slug) {
            return redirect()->route('salon.home', ['salon' => $slug]);
        }

        return redirect('/');
    }
}
