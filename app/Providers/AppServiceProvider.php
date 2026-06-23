<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Grant admin bypass
        Gate::before(function ($user, $ability) {
            if ($user->isAdmin()) {
                return true;
            }
        });

        // Define specific dynamic permissions gates
        $permissions = [
            'manage_users',
            'manage_staff',
            'manage_services',
            'manage_bookings',
            'manage_inventory',
            'manage_feedbacks',
            'manage_roles',
            'create_bookings',
            'view_history',
            'edit_profile',
        ];

        foreach ($permissions as $permission) {
            Gate::define($permission, function ($user) use ($permission) {
                return $user->hasPermission($permission);
            });
        }
    }
}
