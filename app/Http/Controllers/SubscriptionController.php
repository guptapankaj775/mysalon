<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPlan;
use App\Models\SubscriptionSetting;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    /**
     * Show plan selection page.
     */
    public function index()
    {
        $user = Auth::user();

        // If user already has an active plan, redirect to dashboard
        if ($user->hasActivePlan()) {
            return redirect()->route('dashboard')->with('info', 'You already have an active subscription.');
        }

        $plans = SubscriptionPlan::active()->ordered()->get();
        $trialDays = SubscriptionSetting::trialDays();

        return view('subscription.index', compact('plans', 'trialDays'));
    }

    /**
     * Handle plan selection.
     */
    public function selectPlan(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
        ]);

        $user = Auth::user();
        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        // If user already has an active plan, redirect away
        if ($user->hasActivePlan()) {
            return redirect()->route('dashboard')->with('info', 'You already have an active subscription.');
        }

        // Create a pending subscription
        $subscription = UserSubscription::create([
            'user_id'        => $user->id,
            'plan_id'        => $plan->id,
            'status'         => 'pending',
            'payment_status' => 'pending',
            'amount_paid'    => $plan->price,
        ]);

        // Free trial → activate immediately, skip payment
        if ($plan->is_trial || $plan->price == 0) {
            $trialDays = SubscriptionSetting::trialDays();
            $subscription->update([
                'status'         => 'active',
                'payment_status' => 'free',
                'starts_at'      => now(),
                'expires_at'     => now()->addDays($trialDays),
                'amount_paid'    => 0,
            ]);

            return redirect()->route('subscription.success', ['subscription' => $subscription->id]);
        }

        // Paid plan → go to mock payment
        return redirect()->route('subscription.payment', ['subscription' => $subscription->id]);
    }

    /**
     * Show mock payment screen.
     */
    public function payment(UserSubscription $subscription)
    {
        $user = Auth::user();

        // Ensure subscription belongs to this user
        if ($subscription->user_id !== $user->id) {
            abort(403);
        }

        if ($subscription->status !== 'pending') {
            return redirect()->route('dashboard');
        }

        $plan = $subscription->plan;

        return view('subscription.payment', compact('subscription', 'plan'));
    }

    /**
     * Process mock payment.
     */
    public function processPayment(Request $request, UserSubscription $subscription)
    {
        $user = Auth::user();

        if ($subscription->user_id !== $user->id || $subscription->status !== 'pending') {
            abort(403);
        }

        $request->validate([
            'payment_method' => 'required|in:card,upi,netbanking',
        ]);

        // Simulate payment success (mock gateway)
        $subscription->update([
            'status'            => 'active',
            'payment_status'    => 'paid',
            'starts_at'         => now(),
            'expires_at'        => now()->addDays($subscription->plan->duration_days),
            'payment_reference' => 'MOCK-' . strtoupper(Str::random(10)),
        ]);

        return redirect()->route('subscription.success', ['subscription' => $subscription->id]);
    }

    /**
     * Show subscription success page.
     */
    public function success(UserSubscription $subscription)
    {
        $user = Auth::user();

        if ($subscription->user_id !== $user->id) {
            abort(403);
        }

        $plan = $subscription->plan;

        return view('subscription.success', compact('subscription', 'plan'));
    }
}
