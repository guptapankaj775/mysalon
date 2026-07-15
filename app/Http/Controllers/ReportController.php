<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Display the sales report.
     */
    public function salesReport(Request $request)
    {
        $ownerId = Auth::user()->created_by ?: Auth::id();

        // Get filter inputs with defaults
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $staffId = $request->input('staff_id');
        $serviceId = $request->input('service_id');

        // Fetch staff and services for filter dropdowns
        $staffMembers = User::where('role', 'staff')
            ->where('created_by', $ownerId)
            ->orderBy('name')
            ->get();

        $services = Service::where('user_id', $ownerId)
            ->orderBy('name')
            ->get();

        // Build the base query for bookings belonging to this salon
        $query = Booking::with(['service', 'category', 'bookingServices.service', 'bookingServices.staff'])
            ->whereHas('service', function ($q) use ($ownerId) {
                $q->where('user_id', $ownerId);
            });

        // Apply Date Range Filter
        $query->whereBetween('appointment_date', [$startDate, $endDate]);

        // Apply Staff Filter
        if ($staffId) {
            $query->where(function ($q) use ($staffId) {
                $q->where('stylist_id', $staffId)
                  ->orWhereHas('bookingServices', function ($sub) use ($staffId) {
                      $sub->where('staff_id', $staffId);
                  });
            });
        }

        // Apply Service Filter
        if ($serviceId) {
            $query->where(function ($q) use ($serviceId) {
                $q->where('service_id', $serviceId)
                  ->orWhereHas('bookingServices', function ($sub) use ($serviceId) {
                      $sub->where('service_id', $serviceId);
                  });
            });
        }

        // Summary Calculations (cloned from filtered query)
        $totalBookingsCount = $query->clone()->count();
        
        $totalRevenue = $query->clone()
            ->whereIn('status', ['completed', 'closed'])
            ->sum('total_price');

        $paidRevenue = $query->clone()
            ->whereIn('status', ['completed', 'closed'])
            ->where('payment_status', 'paid')
            ->sum('total_price');

        $pendingRevenue = $query->clone()
            ->whereIn('status', ['completed', 'closed'])
            ->where('payment_status', 'pending')
            ->sum('total_price');

        // Group daily sales for Chart.js trend lines
        $chartRawData = $query->clone()
            ->whereIn('status', ['completed', 'closed'])
            ->selectRaw('appointment_date, SUM(total_price) as total_sales')
            ->groupBy('appointment_date')
            ->orderBy('appointment_date', 'asc')
            ->get();

        $chartLabels = [];
        $chartValues = [];

        // Pre-fill daily values between start and end dates to make a smooth chart line
        $currentDate = strtotime($startDate);
        $lastDate = strtotime($endDate);
        $mappedSales = $chartRawData->pluck('total_sales', 'appointment_date')->toArray();

        while ($currentDate <= $lastDate) {
            $formattedDate = date('Y-m-d', $currentDate);
            $chartLabels[] = date('M j', $currentDate);
            $chartValues[] = (float)($mappedSales[$formattedDate] ?? 0);
            $currentDate = strtotime('+1 day', $currentDate);
        }

        // Get paginated list of bookings
        $bookings = $query->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.reports.sales', compact(
            'bookings',
            'staffMembers',
            'services',
            'startDate',
            'endDate',
            'staffId',
            'serviceId',
            'totalBookingsCount',
            'totalRevenue',
            'paidRevenue',
            'pendingRevenue',
            'chartLabels',
            'chartValues'
        ));
    }
}
