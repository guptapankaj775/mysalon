<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\SubscriptionSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userSlug = $user->slug;
        if (!$userSlug && $user->created_by) {
            $owner = \App\Models\User::find($user->created_by);
            if ($owner) {
                $userSlug = $owner->slug;
            }
        }

        // Redirect based on whether the user is super admin or merchant
        $isSalonRoute = request()->attributes->get('is_salon_route', false);
        if ($user->isAdmin()) {
            if ($isSalonRoute) {
                // Stay on salon route if explicitly visited
            } else if (request()->routeIs('dashboard')) {
                return redirect()->route('admin.dashboard');
            }
        } elseif ($userSlug) {
            if (!$isSalonRoute) {
                return redirect()->route('salon.dashboard', ['salon' => $userSlug, 'tab' => request('tab')]);
            }
        } else {
            if ($isSalonRoute) {
                return redirect()->route('dashboard', ['tab' => request('tab')]);
            }
        }

        if ($user->role === 'staff') {
            $assignedServices = \App\Models\BookingService::with(['booking.user', 'service'])
                ->where('staff_id', $user->id)
                ->whereHas('booking', function ($q) {
                    $q->where('status', '!=', 'cancelled');
                })
                ->orderBy('created_at', 'desc')
                ->get();

            return view('admin.staff.dashboard', compact('assignedServices'));
        }

        $user->load('createdInventories');

        // Subscription status
        $owner = $user->created_by ? \App\Models\User::find($user->created_by) : $user;
        $activeSubscription = $owner ? $owner->activeSubscription()->with('plan')->first() : null;
        $hasActivePlan = $user->isAdmin() || ($activeSubscription !== null);
        $limitedFeatures = $hasActivePlan ? [] : SubscriptionSetting::limitedFeatures();
        $noticeMessage   = !$hasActivePlan ? SubscriptionSetting::noticeMessage() : null;

        // If subscription has expired but status is still active, fix it
        if ($activeSubscription && $activeSubscription->expires_at && $activeSubscription->expires_at->isPast()) {
            $activeSubscription->update(['status' => 'expired']);
            $activeSubscription = null;
            $hasActivePlan = false;
            $limitedFeatures = SubscriptionSetting::limitedFeatures();
            $noticeMessage   = SubscriptionSetting::noticeMessage();
        }

        // Get upcoming appointments
        $upcomingAppointments = Booking::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'confirmed', 'assigned', 'in_progress'])
            ->where('appointment_date', '>=', now()->format('Y-m-d'))
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->with(['service', 'category'])
            ->get();

        // Get past appointments
        $pastAppointments = Booking::where('user_id', $user->id)
            ->where(function ($query) {
                $query->where('appointment_date', '<', now()->format('Y-m-d'))
                    ->orWhereIn('status', ['completed', 'closed']);
            })
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->with(['service', 'category'])
            ->get();

        // Calculate total spent amount (only from completed/closed and paid appointments)
        $totalSpent = Booking::where('user_id', $user->id)
            ->whereIn('status', ['completed', 'closed'])
            ->where('payment_status', 'paid')
            ->sum('total_price');

        // Get completed sessions count
        $completedSessions = Booking::where('user_id', $user->id)
            ->whereIn('status', ['completed', 'closed'])
            ->count();

        // Get cancelled appointments count
        $cancelledAppointments = Booking::where('user_id', $user->id)
            ->where('status', 'cancelled')
            ->count();

        // Get time slots for rescheduling
        $timeSlots = $this->getTimeSlots();

        return view('dashboard', compact(
            'user',
            'upcomingAppointments',
            'pastAppointments',
            'totalSpent',
            'completedSessions',
            'cancelledAppointments',
            'timeSlots',
            'hasActivePlan',
            'activeSubscription',
            'limitedFeatures',
            'noticeMessage'
        ));
    }

    private function getTimeSlots()
    {
        $slots = [];
        $start = strtotime('09:00');
        $end = strtotime('20:00');
        $interval = 30 * 60; // 30 minutes

        for ($time = $start; $time <= $end; $time += $interval) {
            $slots[] = date('H:i', $time);
        }

        return $slots;
    }
}
