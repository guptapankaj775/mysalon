<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\SubscriptionSetting;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    // ─── Plans CRUD ─────────────────────────────────────────────────────────────

    public function index()
    {
        $plans = SubscriptionPlan::withCount('subscriptions')->ordered()->get();
        return view('admin.subscriptions.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.subscriptions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'price'        => 'required|numeric|min:0',
            'duration_days'=> 'required|integer|min:1',
            'is_trial'     => 'boolean',
            'is_active'    => 'boolean',
            'sort_order'   => 'integer|min:0',
            'features'     => 'nullable|string',
        ]);

        $validated['slug']     = Str::slug($validated['name']) . '-' . time();
        $validated['is_trial'] = $request->boolean('is_trial');
        $validated['is_active']= $request->boolean('is_active', true);
        $validated['features'] = $this->parseFeatures($request->input('features'));

        SubscriptionPlan::create($validated);

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Subscription plan created successfully.');
    }

    public function edit(SubscriptionPlan $subscription)
    {
        return view('admin.subscriptions.edit', compact('subscription'));
    }

    public function update(Request $request, SubscriptionPlan $subscription)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'price'        => 'required|numeric|min:0',
            'duration_days'=> 'required|integer|min:1',
            'is_trial'     => 'boolean',
            'is_active'    => 'boolean',
            'sort_order'   => 'integer|min:0',
            'features'     => 'nullable|string',
        ]);

        $validated['is_trial']  = $request->boolean('is_trial');
        $validated['is_active'] = $request->boolean('is_active');
        $validated['features']  = $this->parseFeatures($request->input('features'));

        $subscription->update($validated);

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Subscription plan updated successfully.');
    }

    public function destroy(SubscriptionPlan $subscription)
    {
        if ($subscription->subscriptions()->where('status', 'active')->exists()) {
            return redirect()->route('admin.subscriptions.index')
                ->with('error', 'Cannot delete a plan with active subscribers.');
        }

        $subscription->delete();

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Subscription plan deleted successfully.');
    }

    // ─── Subscribers List ────────────────────────────────────────────────────────

    public function subscribers(Request $request)
    {
        $query = UserSubscription::with(['user', 'plan']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('plan_id')) {
            $query->where('plan_id', $request->plan_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $subscribers = $query->orderByDesc('created_at')->paginate(15);
        $plans = SubscriptionPlan::active()->get();

        // Stats
        $stats = [
            'total'   => UserSubscription::count(),
            'active'  => UserSubscription::where('status', 'active')->count(),
            'expired' => UserSubscription::where('status', 'expired')->count(),
            'trial'   => UserSubscription::whereHas('plan', fn($q) => $q->where('is_trial', true))
                            ->where('status', 'active')->count(),
        ];

        return view('admin.subscriptions.subscribers', compact('subscribers', 'plans', 'stats'));
    }

    /**
     * Update the status of a subscription manually.
     */
    public function updateSubscriptionStatus(Request $request, UserSubscription $userSubscription)
    {
        $request->validate([
            'status' => 'required|in:active,expired,cancelled,pending',
        ]);

        $userSubscription->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Subscription status updated.');
    }

    // ─── Settings ────────────────────────────────────────────────────────────────

    public function settings()
    {
        $trialDays       = SubscriptionSetting::trialDays();
        $limitedFeatures = SubscriptionSetting::limitedFeatures();
        $noticeMessage   = SubscriptionSetting::noticeMessage();

        $allFeatures = [
            'booking'      => 'Book New Appointments',
            'appointments' => 'View Full Appointment History',
            'inventory'    => 'Inventory Access',
            'profile'      => 'Profile Editing',
        ];

        return view('admin.subscriptions.settings', compact(
            'trialDays',
            'limitedFeatures',
            'noticeMessage',
            'allFeatures'
        ));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'trial_days'      => 'required|integer|min:1|max:365',
            'limited_features'=> 'nullable|array',
            'notice_message'  => 'required|string|max:500',
        ]);

        SubscriptionSetting::set('trial_days', $request->trial_days, 'integer');
        SubscriptionSetting::set('limited_features', $request->limited_features ?? [], 'json');
        SubscriptionSetting::set('notice_message', $request->notice_message, 'string');

        return redirect()->route('admin.subscription.settings')
            ->with('success', 'Subscription settings updated successfully.');
    }

    // ─── Private Helpers ─────────────────────────────────────────────────────────

    private function parseFeatures(?string $featuresText): array
    {
        if (empty($featuresText)) return [];
        return array_filter(array_map('trim', explode("\n", $featuresText)));
    }
}
