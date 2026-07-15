<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RolePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class RolePermissionController extends Controller
{
    /**
     * Display a listing of the roles and permissions matrix.
     */
    public function index()
    {
        Gate::authorize('manage_roles');

        $roles = ['admin', 'staff', 'user'];

        $permissions = [
            'manage_users' => 'Manage Users',
            'manage_staff' => 'Manage Staff',
            'manage_services' => 'Manage Services',
            'manage_bookings' => 'Manage Bookings',
            'manage_inventory' => 'Manage Inventory',
            'manage_feedbacks' => 'Manage Feedbacks',
            'manage_roles' => 'Manage Roles & Permissions',
            'create_bookings' => 'Create Bookings',
            'view_history' => 'View History',
            'edit_profile' => 'Edit Profile',
        ];

        $ownerId = \Illuminate\Support\Facades\Auth::user()->isAdmin() ? null : \Illuminate\Support\Facades\Auth::id();

        // Retrieve custom permissions for this salon owner
        $rolePermissionsQuery = RolePermission::query();
        if ($ownerId !== null && RolePermission::where('created_by', $ownerId)->exists()) {
            $rolePermissionsQuery->where('created_by', $ownerId);
        } else {
            $rolePermissionsQuery->whereNull('created_by');
        }

        $rolePermissions = $rolePermissionsQuery->get()->groupBy('role')->map(function ($items) {
            return $items->pluck('permission')->toArray();
        })->toArray();

        return view('admin.roles.index', compact('roles', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the roles and permissions mapping.
     */
    public function update(Request $request)
    {
        Gate::authorize('manage_roles');

        $request->validate([
            'permissions' => 'array',
        ]);

        $ownerId = \Illuminate\Support\Facades\Auth::user()->isAdmin() ? null : \Illuminate\Support\Facades\Auth::id();

        DB::transaction(function () use ($request, $ownerId) {
            // Clear existing permissions mappings safely for this owner
            if ($ownerId === null) {
                RolePermission::whereNull('created_by')->delete();
            } else {
                RolePermission::where('created_by', $ownerId)->delete();
            }

            $permissionsData = $request->input('permissions', []);
            foreach ($permissionsData as $role => $rolePermissionsList) {
                // Validate role name
                if (!in_array($role, ['admin', 'staff', 'user'])) {
                    continue;
                }
                
                foreach (array_keys($rolePermissionsList) as $permission) {
                    RolePermission::create([
                        'role' => $role,
                        'permission' => $permission,
                        'created_by' => $ownerId,
                    ]);
                }
            }
        });

        return redirect()->route('admin.roles.index')->with('success', 'Roles and permissions updated successfully!');
    }
}
