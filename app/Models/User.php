<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'phone',
        'gst_number',
        'has_no_gst',
        'billing_name',
        'trade_name',
        'billing_address',
        'location',
        'latitude',
        'longitude',
        'password',
        'profile_photo',
        'role',
        'is_verified',
        'created_by',
        'salon_name',
        'slug',
        'salon_type',
        'salon_model',
        'franchisee_name',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_verified' => 'boolean',
            'has_no_gst' => 'boolean',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's bookings.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get the inventory items created by the user.
     */
    public function createdInventories()
    {
        return $this->hasMany(Inventory::class, 'user_id');
    }

    /**
     * Get all subscriptions for the user.
     */
    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    /**
     * Get the user's active subscription.
     */
    public function activeSubscription()
    {
        return $this->hasOne(UserSubscription::class)
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
            })
            ->latest();
    }

    /**
     * Check if the user has an active subscription plan.
     */
    public function hasActivePlan(): bool
    {
        return $this->activeSubscription()->exists();
    }

    /**
     * Check if the user is a Super Admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->email === 'admin@salonjc.com' || $this->role === 'super_admin';
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->isSuperAdmin();
    }

    /**
     * Check if the user has a specific permission.
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $ownerId = $this->created_by ?? $this->id;

        // Check if there are ANY customized role permissions for this salon (created_by) and role
        $hasCustom = RolePermission::where('created_by', $ownerId)
            ->where('role', $this->role)
            ->exists();

        if ($hasCustom) {
            return RolePermission::where('created_by', $ownerId)
                ->where('role', $this->role)
                ->where('permission', $permission)
                ->exists();
        }

        // Fall back to system defaults (created_by is null)
        return RolePermission::whereNull('created_by')
            ->where('role', $this->role)
            ->where('permission', $permission)
            ->exists();
    }

    /**
     * Check if the user has a specific role.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function assignedServices()
    {
        return $this->hasMany(BookingService::class, 'staff_id');
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::saving(function ($user) {
            if ($user->isDirty('salon_name') && $user->salon_name) {
                if ($user->role === 'staff') {
                    $user->slug = null;
                    return;
                }
                $baseSlug = Str::slug($user->salon_name);
                $slug = $baseSlug;
                $count = 1;
                while (static::where('slug', $slug)->where('id', '!=', $user->id)->exists()) {
                    $slug = $baseSlug . '-' . $count++;
                }
                $user->slug = $slug;
            }
        });
    }
}
