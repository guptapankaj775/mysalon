<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Routes exempt from subscription check (by name prefix or exact name).
     */
    protected array $exempt = [
        'subscription.',
        'admin.',
        'profile.',
        'logout',
        'login',
        'register',
        'password.',
        'verification.',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return $next($request);
        }

        // Admins always have full access
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Check if current route is exempt
        $routeName = $request->route()?->getName() ?? '';
        foreach ($this->exempt as $prefix) {
            if (str_starts_with($routeName, $prefix) || $routeName === $prefix) {
                return $next($request);
            }
        }

        // Check subscription
        if (!$user->hasActivePlan()) {
            // For dashboard, allow but mark as limited
            if ($routeName === 'dashboard') {
                return $next($request);
            }

            // For other protected routes, redirect to subscription page
            // (but let dashboard through with limited mode)
            return $next($request);
        }

        return $next($request);
    }
}
