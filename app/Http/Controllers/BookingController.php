<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Specialist;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $salon = request()->attributes->get('salon');

        $categoriesQuery = ServiceCategory::query();
        if ($salon) {
            // Check if salon has any custom categories
            $hasCustom = ServiceCategory::where('user_id', $salon->id)->exists();
            if ($hasCustom) {
                $categoriesQuery->where('user_id', $salon->id);
            } else {
                $categoriesQuery->whereNull('user_id');
            }
        } else {
            $categoriesQuery->whereNull('user_id');
        }
        $categories = $categoriesQuery->get();

        $selectedCategory = null;
        $selectedService = null;
        $services = collect();

        // If category is selected, get its services
        if ($request->has('serviceCategory')) {
            $selectedCategory = ServiceCategory::find($request->serviceCategory);
            if ($selectedCategory) {
                $servicesQuery = Service::where('category_id', $selectedCategory->id);
                if ($salon) {
                    $hasCustom = Service::where('user_id', $salon->id)->exists();
                    if ($hasCustom) {
                        $servicesQuery->where('user_id', $salon->id);
                    } else {
                        $servicesQuery->whereNull('user_id');
                    }
                } else {
                    $servicesQuery->whereNull('user_id');
                }
                $services = $servicesQuery->get();
            }
        }

        // If service is specified in URL
        if ($request->has('service')) {
            $selectedService = Service::find($request->service);
            if ($selectedService) {
                $selectedCategory = $selectedService->category;
                $servicesQuery = Service::where('category_id', $selectedCategory->id);
                if ($salon) {
                    $hasCustom = Service::where('user_id', $salon->id)->exists();
                    if ($hasCustom) {
                        $servicesQuery->where('user_id', $salon->id);
                    } else {
                        $servicesQuery->whereNull('user_id');
                    }
                } else {
                    $servicesQuery->whereNull('user_id');
                }
                $services = $servicesQuery->get();
            }
        }

        $timeSlots = $this->getTimeSlots();
        return view('booking', compact(
            'categories',
            'services',
            'timeSlots',
            'selectedService',
            'selectedCategory'
        ));
    }

    public function store(Request $request)
    {
        $salon = request()->attributes->get('salon');

        $rules = [
            'fullName' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email',
            'serviceCategory' => 'required',
            'service' => 'required',
            'appointmentDate' => 'required|date|after_or_equal:today',
            'appointmentTime' => 'required',
            'termsAccept' => 'required|accepted'
        ];

        // If not a salon-scoped route, we require selecting a salon from the dropdown
        if (!$salon) {
            $rules['salon_id'] = 'required|exists:users,id';
        }

        $request->validate($rules);

        // Determine the salon ID
        $salonId = $salon ? $salon->id : $request->salon_id;

        // Get the chosen service category and service (could be global or salon-specific)
        $globalCategory = ServiceCategory::findOrFail($request->serviceCategory);
        $globalService = Service::findOrFail($request->service);

        // Ensure category and service are owned by this salon, or copy them over on-write
        if ($globalCategory->user_id != $salonId) {
            $salonCategory = ServiceCategory::where('user_id', $salonId)
                ->where('name', $globalCategory->name)
                ->first();
            if (!$salonCategory) {
                $salonCategory = ServiceCategory::create([
                    'name' => $globalCategory->name,
                    'icon_class' => $globalCategory->icon_class,
                    'description' => $globalCategory->description,
                    'long_description' => $globalCategory->long_description,
                    'start_price' => $globalCategory->start_price,
                    'status' => true,
                    'user_id' => $salonId,
                ]);
            }
        } else {
            $salonCategory = $globalCategory;
        }

        if ($globalService->user_id != $salonId) {
            $salonService = Service::where('user_id', $salonId)
                ->where('category_id', $salonCategory->id)
                ->where('name', $globalService->name)
                ->first();
            if (!$salonService) {
                $salonService = Service::create([
                    'name' => $globalService->name,
                    'description' => $globalService->description,
                    'duration' => $globalService->duration,
                    'price' => $globalService->price,
                    'category_id' => $salonCategory->id,
                    'status' => true,
                    'features' => $globalService->features,
                    'user_id' => $salonId,
                ]);
            }
        } else {
            $salonService = $globalService;
        }

        // Get authenticated user (if any)
        $user = Auth::user();

        $basePrice = $salonService->price;
        $serviceFee = $basePrice * 0.03; // 3% service fee
        $totalPrice = $basePrice + $serviceFee;

        $booking = Booking::create([
            'user_id' => $user ? $user->id : null,
            'full_name' => $request->fullName,
            'phone' => $request->phone,
            'email' => $request->email,
            'service_category_id' => $salonCategory->id,
            'service_id' => $salonService->id,
            'appointment_date' => $request->appointmentDate,
            'appointment_time' => $request->appointmentTime,
            'stylist_id' => null,
            'special_requirements' => $request->requirements,
            'base_price' => $basePrice,
            'addons_price' => $serviceFee,
            'total_price' => $totalPrice,
            'status' => 'pending',
            'payment_status' => 'pending'
        ]);

        // Redirect to payment page with booking ID
        if ($salon) {
            return redirect()->route('salon.booking.payment', ['salon' => $salon->slug, 'id' => $booking->id]);
        }
        return redirect()->route('booking.payment', $booking->id);
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

    public function showPayment($id)
    {
        $booking = Booking::findOrFail($id);

        // Only show payment page for pending payments
        if ($booking->payment_status !== 'pending') {
            $salon = request()->attributes->get('salon');
            if ($salon) {
                if (auth()->check()) {
                    return redirect()->route('salon.dashboard', ['salon' => $salon->slug])
                        ->with('error', 'This booking has already been paid for.');
                }
                return redirect()->route('salon.home', ['salon' => $salon->slug])
                    ->with('error', 'This booking has already been paid for.');
            }
            if (auth()->check()) {
                return redirect()->route('dashboard')
                    ->with('error', 'This booking has already been paid for.');
            }
            return redirect()->route('home')
                ->with('error', 'This booking has already been paid for.');
        }

        return view('booking.payment', compact('booking'));
    }

    public function processPayment(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        // Only process pending payments
        if ($booking->payment_status !== 'pending') {
            $salon = request()->attributes->get('salon');
            if ($salon) {
                if (auth()->check()) {
                    return redirect()->route('salon.dashboard', ['salon' => $salon->slug])
                        ->with('error', 'This booking has already been paid for.');
                }
                return redirect()->route('salon.home', ['salon' => $salon->slug])
                    ->with('error', 'This booking has already been paid for.');
            }
            if (auth()->check()) {
                return redirect()->route('dashboard')
                    ->with('error', 'This booking has already been paid for.');
            }
            return redirect()->route('home')
                ->with('error', 'This booking has already been paid for.');
        }

        $paymentOption = $request->input('payment_option', 'online');

        if ($paymentOption === 'pay_at_shop') {
            $booking->update([
                'payment_status' => 'pending',
                'payment_method' => 'pay_at_shop',
                'transaction_id' => 'SHOP_' . strtoupper(uniqid())
            ]);
        } else {
            $request->validate([
                'card_number' => 'required|string|size:16',
                'card_expiry' => 'required|string|size:5', // MM/YY format
                'card_cvv' => 'required|string|size:3',
                'card_holder' => 'required|string|max:255',
            ]);

            // In a real application, you would process the payment with a payment gateway here
            // For this example, we'll simulate a successful payment
            $booking->update([
                'payment_status' => 'paid',
                'payment_method' => 'credit_card',
                'transaction_id' => 'TXN_' . uniqid()
                // Status remains 'pending' until admin confirms
            ]);
        }

        $salon = request()->attributes->get('salon');
        if ($salon) {
            return redirect()->route('salon.booking.payment.success', ['salon' => $salon->slug, 'id' => $booking->id]);
        }
        return redirect()->route('booking.payment.success', $booking->id);
    }

    public function paymentSuccess($id)
    {
        $booking = Booking::findOrFail($id);
        return view('booking.success', compact('booking'));
    }

    public function cancel($id)
    {
        $booking = Booking::findOrFail($id);

        // Only allow cancellation if booking is pending and not yet cancelled
        if ($booking->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending bookings can be cancelled.');
        }

        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now()
        ]);

        return redirect()->back()->with('success', 'Booking cancelled successfully.');
    }

    public function reschedule(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        // Only allow rescheduling if booking is confirmed
        if ($booking->status !== 'confirmed') {
            return response()->json(['message' => 'Only confirmed bookings can be rescheduled.'], 400);
        }

        // Extend the booking date by 1 day
        $currentDate = \Carbon\Carbon::parse($booking->appointment_date);
        $newDate = $currentDate->addDay();

        $booking->update([
            'appointment_date' => $newDate->format('Y-m-d')
        ]);

        return redirect()->back()->with('success', 'Booking rescheduled successfully.');
    }

    public function showInvoice($id)
    {
        $booking = Booking::with('service')->findOrFail($id);

        // Check if the user is authorized to view this invoice
        $isAuthorized = false;
        if (auth()->check()) {
            $user = auth()->user();
            $ownerId = $user->created_by ?: $user->id;
            $isAuthorized = ($booking->user_id === $user->id) ||
                            $user->isAdmin() ||
                            ($booking->service && $booking->service->user_id === $ownerId);
        } else {
            // For guest users, allow if booking has no user_id (it was a guest booking)
            $isAuthorized = ($booking->user_id === null);
        }

        if (!$isAuthorized) {
            abort(403, 'Unauthorized action.');
        }

        // Only allow viewing invoice if the payment is paid OR booking is completed/closed
        if ($booking->payment_status !== 'paid' && !in_array($booking->status, ['closed', 'completed'])) {
            $salon = request()->attributes->get('salon');
            if ($salon) {
                if (auth()->check()) {
                    return redirect()->route('salon.dashboard', ['salon' => $salon->slug])
                        ->with('error', 'This booking has not been paid for yet.');
                }
                return redirect()->route('salon.home', ['salon' => $salon->slug])
                    ->with('error', 'This booking has not been paid for yet.');
            }
            if (auth()->check()) {
                return redirect()->route('dashboard')
                    ->with('error', 'This booking has not been paid for yet.');
            }
            return redirect()->route('home')
                ->with('error', 'This booking has not been paid for yet.');
        }

        // Retrieve or create the corresponding sales invoice automatically
        $salesInvoice = \App\Models\SalesInvoice::firstOrCreate(
            ['invoice_number' => 'INV-BOOK-' . $booking->id],
            [
                'customer_name' => $booking->full_name,
                'amount' => $booking->total_price,
                'status' => $booking->payment_status === 'paid' ? 'paid' : 'pending',
            ]
        );

        if ($salesInvoice->status !== ($booking->payment_status === 'paid' ? 'paid' : 'pending')) {
            $salesInvoice->update(['status' => $booking->payment_status === 'paid' ? 'paid' : 'pending']);
        }

        return view('booking.invoice', compact('booking', 'salesInvoice'));
    }

    public function getSalons(Request $request)
    {
        $query = User::whereNotNull('salon_name')
            ->where('role', 'user')
            ->where('is_verified', true);

        $lat = $request->input('latitude');
        $lng = $request->input('longitude');
        $city = $request->input('city');

        if ($city) {
            $query->where('location', 'like', '%' . $city . '%');
        }

        $salons = $query->get();

        if ($lat && $lng) {
            $salons = $salons->map(function ($salon) use ($lat, $lng) {
                if ($salon->latitude && $salon->longitude) {
                    $salon->distance = $this->calculateDistance($lat, $lng, $salon->latitude, $salon->longitude);
                } else {
                    $salon->distance = 999999;
                }
                return $salon;
            })->sortBy('distance')->values();
        }

        return response()->json($salons);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function getSalonCategories($id)
    {
        $hasCustom = ServiceCategory::where('user_id', $id)->exists();
        if ($hasCustom) {
            $categories = ServiceCategory::where('user_id', $id)->where('status', true)->get();
        } else {
            $categories = ServiceCategory::whereNull('user_id')->where('status', true)->get();
        }
        return response()->json($categories);
    }

    public function getSalonServices($id, $categoryId)
    {
        $hasCustom = Service::where('user_id', $id)->exists();
        if ($hasCustom) {
            $services = Service::where('user_id', $id)
                ->where('category_id', $categoryId)
                ->where('status', true)
                ->get();
        } else {
            $services = Service::whereNull('user_id')
                ->where('category_id', $categoryId)
                ->where('status', true)
                ->get();
        }
        return response()->json($services);
    }

    public function dashboardServices()
    {
        $salon = request()->attributes->get('salon');

        $categoriesQuery = \App\Models\ServiceCategory::query()->with(['services' => function ($query) use ($salon) {
            $query->where('status', true)->with(['images', 'icon']);
            if ($salon) {
                $query->where('user_id', $salon->id);
            }
        }]);

        if ($salon) {
            $categoriesQuery->where('user_id', $salon->id);
        }

        $categories = $categoriesQuery->get();

        return view('customer.services.book', compact('categories'));
    }

    public function dashboardCreateBooking(Request $request)
    {
        $salon = request()->attributes->get('salon');
        $ownerId = $salon ? $salon->id : null;

        $categoriesQuery = \App\Models\ServiceCategory::query();
        if ($ownerId) {
            $categoriesQuery->where('user_id', $ownerId);
        }
        $categories = $categoriesQuery->get();

        $selectedCategory = null;
        $selectedService = null;
        $services = collect();

        // If category is selected, get its services
        if ($request->has('serviceCategory')) {
            $selectedCategory = \App\Models\ServiceCategory::find($request->serviceCategory);
            if ($selectedCategory) {
                $servicesQuery = \App\Models\Service::where('category_id', $selectedCategory->id);
                if ($ownerId) {
                    $servicesQuery->where('user_id', $ownerId);
                }
                $services = $servicesQuery->get();
            }
        }

        // If service is specified in URL
        if ($request->has('service')) {
            $selectedService = \App\Models\Service::find($request->service);
            if ($selectedService) {
                $selectedCategory = $selectedService->category;
                $servicesQuery = \App\Models\Service::where('category_id', $selectedCategory->id);
                if ($ownerId) {
                    $servicesQuery->where('user_id', $ownerId);
                }
                $services = $servicesQuery->get();
            }
        }

        // Generate time slots
        $timeSlots = [];
        $start = new \DateTime('09:00');
        $end = new \DateTime('20:00');
        $interval = new \DateInterval('PT30M');
        $current = clone $start;

        while ($current <= $end) {
            $timeSlots[] = $current->format('H:i');
            $current->add($interval);
        }

        return view('customer.bookings.create', compact(
            'categories',
            'services',
            'timeSlots',
            'selectedService',
            'selectedCategory'
        ));
    }

    public function dashboardStoreBooking(Request $request)
    {
        $request->validate([
            'fullName' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email',
            'serviceCategory' => 'required|exists:service_categories,id',
            'service' => 'required|exists:services,id',
            'appointmentDate' => 'required|date|after_or_equal:today',
            'appointmentTime' => 'required',
        ]);

        $user = \Illuminate\Support\Facades\Auth::user();
        $service = \App\Models\Service::findOrFail($request->service);
        $basePrice = $service->price;
        $serviceFee = $basePrice * 0.03; // 3% service fee
        $totalPrice = $basePrice + $serviceFee;

        $booking = Booking::create([
            'user_id' => $user->id,
            'full_name' => $request->fullName,
            'phone' => $request->phone,
            'email' => $request->email,
            'service_category_id' => $request->serviceCategory,
            'service_id' => $request->service,
            'appointment_date' => $request->appointmentDate,
            'appointment_time' => $request->appointmentTime,
            'stylist_id' => null,
            'special_requirements' => $request->requirements,
            'base_price' => $basePrice,
            'addons_price' => $serviceFee,
            'total_price' => $totalPrice,
            'status' => 'pending',
            'payment_status' => 'pending'
        ]);

        $salon = request()->attributes->get('salon');
        if ($salon) {
            return redirect()->route('salon.booking.payment', ['salon' => $salon->slug, 'id' => $booking->id]);
        }
        return redirect()->route('booking.payment', $booking->id);
    }
}
