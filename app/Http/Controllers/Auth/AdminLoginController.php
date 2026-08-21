<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminLoginController extends Controller
{
    /**
     * Display the admin login view.
     */
    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            if (Auth::user()->isSuperAdmin()) {
                return redirect()->to('/dashboard');
            }
            return redirect()->route('dashboard');
        }

        return view('auth.admin-login');
    }

    /**
     * Handle an incoming super admin authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        // Super Admin Restriction: ONLY admin@salonjc.com or super_admin role can log in here
        if (!$user->isSuperAdmin()) {
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('superadmin.login')
                ->withErrors(['email' => 'Access denied. This portal is strictly restricted to Super Admin (admin@salonjc.com).']);
        }

        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
    }
}
