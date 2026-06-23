<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'duration_days',
        'is_trial',
        'is_active',
        'features',
        'sort_order',
    ];

    protected $casts = [
        'is_trial'   => 'boolean',
        'is_active'  => 'boolean',
        'features'   => 'array',
        'price'      => 'decimal:2',
    ];

    // Relationships
    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class, 'plan_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeTrial($query)
    {
        return $query->where('is_trial', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('price');
    }

    // Helpers
    public function isFree(): bool
    {
        return $this->price == 0;
    }

    public function getFormattedPriceAttribute(): string
    {
        if ($this->price == 0) {
            return 'Free';
        }
        return '₹' . number_format($this->price, 0);
    }
}
