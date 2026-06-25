<?php

namespace Database\Seeders;

use App\Models\RolePermission;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        RolePermission::query()->delete();

        $defaultPermissions = [
            'admin' => [
                'manage_users',
                'manage_staff',
                'manage_services',
                'manage_bookings',
                'manage_inventory',
                'manage_feedbacks',
                'manage_roles',
                'manage_vendors',
            ],
            'user' => [
                'create_bookings',
                'view_history',
                'edit_profile',
            ],
            'staff' => [
                'manage_bookings',
                'manage_inventory',
            ]
        ];

        foreach ($defaultPermissions as $role => $permissions) {
            foreach ($permissions as $permission) {
                RolePermission::create([
                    'role' => $role,
                    'permission' => $permission,
                ]);
            }
        }
    }
}
