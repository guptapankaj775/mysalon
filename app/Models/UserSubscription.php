<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'status',
        'starts_at',
        'expires_at',
        'payment_status',
        'payment_reference',
        'amount_paid',
        'notes',
    ];

    protected $casts = [
        'starts_at'  => 'datetime',
        'expires_at' => 'datetime',
        'amount_paid' => 'decimal:2',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    // Helpers
    public function isActive(): bool
    {
        return $this->status === 'active'
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired'
            || ($this->expires_at !== null && $this->expires_at->isPast());
    }

    public function getDaysRemainingAttribute(): int
    {
        if (!$this->expires_at) return 0;
        return max(0, now()->diffInDays($this->expires_at, false));
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'active'    => '<span class="badge bg-success">Active</span>',
            'expired'   => '<span class="badge bg-danger">Expired</span>',
            'cancelled' => '<span class="badge bg-secondary">Cancelled</span>',
            'pending'   => '<span class="badge bg-warning text-dark">Pending</span>',
            default     => '<span class="badge bg-light text-dark">' . ucfirst($this->status) . '</span>',
        };
    }
}
