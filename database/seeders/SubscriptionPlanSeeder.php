<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use App\Models\SubscriptionSetting;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Default Plans ──────────────────────────────────────────────────────
        $plans = [
            [
                'name'         => 'Free Trial',
                'slug'         => 'free-trial',
                'description'  => 'Try all features free for a limited time. No credit card required.',
                'price'        => 0,
                'duration_days'=> 14,
                'is_trial'     => true,
                'is_active'    => true,
                'sort_order'   => 0,
                'features'     => [
                    'Full dashboard access',
                    'Unlimited bookings',
                    'Inventory management',
                    'Customer management',
                    'Email support',
                ],
            ],
            [
                'name'         => 'Basic',
                'slug'         => 'basic',
                'description'  => 'Perfect for small salons getting started.',
                'price'        => 999,
                'duration_days'=> 30,
                'is_trial'     => false,
                'is_active'    => true,
                'sort_order'   => 1,
                'features'     => [
                    'Full dashboard access',
                    'Up to 100 bookings/month',
                    'Inventory management',
                    'Customer management',
                    'Email support',
                    'Monthly reports',
                ],
            ],
            [
                'name'         => 'Pro',
                'slug'         => 'pro',
                'description'  => 'For growing salons that need advanced features.',
                'price'        => 2499,
                'duration_days'=> 30,
                'is_trial'     => false,
                'is_active'    => true,
                'sort_order'   => 2,
                'features'     => [
                    'Everything in Basic',
                    'Unlimited bookings',
                    'Advanced analytics',
                    'Priority support',
                    'Custom branding',
                    'Staff management',
                    'SMS notifications',
                ],
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }

        // ─── Default Settings ───────────────────────────────────────────────────
        $settings = [
            [
                'key'         => 'trial_days',
                'value'       => '14',
                'type'        => 'integer',
                'label'       => 'Free Trial Duration (days)',
                'description' => 'Number of days for the free trial period.',
            ],
            [
                'key'         => 'limited_features',
                'value'       => json_encode(['booking', 'appointments', 'inventory']),
                'type'        => 'json',
                'label'       => 'Limited Features for Non-Subscribers',
                'description' => 'Dashboard sections that are locked without an active plan.',
            ],
            [
                'key'         => 'notice_message',
                'value'       => '⚠️ Your subscription is inactive. Some features are limited. Please select a plan to unlock full access.',
                'type'        => 'string',
                'label'       => 'Notice Message',
                'description' => 'Message shown to users without an active subscription.',
            ],
        ];

        foreach ($settings as $setting) {
            SubscriptionSetting::updateOrCreate(['key' => $setting['key']], $setting);
        }

        $this->command->info('✅ Subscription plans and settings seeded successfully.');
    }
}
