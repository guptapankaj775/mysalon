<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use App\Models\Specialist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        // Get today's bookings count
        $todayBookings = Booking::whereDate('appointment_date', today())->count();

        // Get total revenue
        $totalRevenue = Booking::where('payment_status', 'paid')->sum('total_price');

        // Get total active services
        $totalServices = Service::count();

        // Get total customers (unique customers from bookings)
        $totalCustomers = Booking::distinct('email')->count('email');

        // Get recent bookings
        $recentBookings = Booking::with(['service', 'category'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get popular services
        $popularServices = Service::query()
            ->select('services.*')
            ->withCount('bookings')
            ->with('category')
            ->selectSub(function ($query) {
                $query->from('bookings')
                    ->selectRaw('COALESCE(SUM(total_price), 0)')
                    ->whereColumn('bookings.service_id', 'services.id')
                    ->where('payment_status', 'paid');
            }, 'revenue')
            ->orderByDesc('bookings_count')
            ->take(5)
            ->get();

        return view('admin.index', compact(
            'todayBookings',
            'totalRevenue',
            'totalServices',
            'totalCustomers',
            'recentBookings',
            'popularServices'
        ));
    }

    public function services()
    {
        $services = Service::query()
            ->select('services.*')
            ->withCount('bookings')
            ->with(['category', 'icon'])
            ->selectSub(function ($query) {
                $query->from('bookings')
                    ->selectRaw('COALESCE(SUM(total_price), 0)')
                    ->whereColumn('bookings.service_id', 'services.id')
                    ->where('payment_status', 'paid');
            }, 'revenue')
            ->orderBy('services.name')
            ->get();

        return view('admin.services.index', compact('services'));
    }

    public function createService()
    {
        $categories = ServiceCategory::all();
        return view('admin.services.create', compact('categories'));
    }

    public function storeService(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'features' => 'nullable|array',
            'duration' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:service_categories,id',
            'status' => 'boolean',
            'icon' => 'required|string|max:50'
        ]);

        $iconPath = $validated['icon'];
        unset($validated['icon']);

        $service = Service::create($validated);
        $service->icon()->create(['image_path' => $iconPath]);

        return redirect()->route('admin.services')->with('success', 'Service created successfully');
    }

    public function editService(Service $service)
    {
        $categories = ServiceCategory::all();
        return view('admin.services.edit', compact('service', 'categories'));
    }

    public function updateService(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'features' => 'nullable|array',
            'duration' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:service_categories,id',
            'status' => 'boolean',
            'icon' => 'required|string|max:50'
        ]);

        $iconPath = $validated['icon'];
        unset($validated['icon']);

        $service->update($validated);

        // Update or create icon
        if ($service->icon) {
            $service->icon->update(['image_path' => $iconPath]);
        } else {
            $service->icon()->create(['image_path' => $iconPath]);
        }

        return redirect()->route('admin.services')->with('success', 'Service updated successfully');
    }

    public function destroyService(Service $service)
    {
        $service->delete();
        return redirect()->route('admin.services')->with('success', 'Service deleted successfully');
    }

    public function bookings(Request $request)
    {
        $query = Booking::with(['service', 'category']);

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.bookings.index', compact('bookings'));
    }

    public function showBooking(Booking $booking)
    {
        $booking->load(['service', 'category']);
        return view('admin.bookings.show', compact('booking'));
    }

    public function updateBookingStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed',
            'payment_status' => 'required|in:pending,paid,failed'
        ]);

        $booking->update($validated);
        return redirect()->back()->with('success', 'Booking status updated successfully');
    }

    public function confirmBooking(Booking $booking)
    {
        if ($booking->status !== 'pending' || $booking->payment_status !== 'paid') {
            return redirect()->back()->with('error', 'Only pending bookings with paid status can be confirmed.');
        }

        $booking->update([
            'status' => 'confirmed',
            'confirmed_at' => now()
        ]);

        return redirect()->back()->with('success', 'Booking has been confirmed successfully.');
    }

    public function rejectBooking(Booking $booking)
    {
        if ($booking->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending bookings can be rejected.');
        }

        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now()
        ]);

        return redirect()->back()->with('success', 'Booking has been rejected successfully.');
    }

    public function cancelBooking(Booking $booking)
    {
        if ($booking->status === 'cancelled') {
            return redirect()->back()->with('error', 'This booking is already cancelled.');
        }

        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now()
        ]);

        return redirect()->back()->with('success', 'Booking has been cancelled successfully.');
    }

    public function completeBooking(Booking $booking)
    {
        if ($booking->status !== 'confirmed') {
            return redirect()->back()->with('error', 'Only confirmed bookings can be marked as completed.');
        }

        $booking->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);

        return redirect()->back()->with('success', 'Booking has been marked as completed successfully.');
    }

    // User Management Methods
    public function users()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        return view('admin.users.create');
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role'     => 'required|in:user,admin,staff',
            'is_verified' => 'nullable|boolean',
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'],
            'is_verified' => $request->boolean('is_verified'),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }



    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:user,admin,staff',
            'is_verified' => 'nullable|boolean',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'is_verified' => $user->id === Auth::id() ? true : $request->boolean('is_verified'),
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = bcrypt($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')
            ->with('user_updated', 'User updated successfully.');
    }

    public function toggleUserVerification(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot change verification for your own account.');
        }

        $user->update([
            'is_verified' => ! $user->is_verified,
        ]);

        return redirect()->route('admin.users.index')
            ->with('user_updated', 'User verification status updated successfully.');
    }

    public function destroyUser(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('user_deleted', 'User deleted successfully.');
    }

    // Staff (Specialist) Management Methods
    public function staff()
    {
        $staff = Specialist::with('services')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.staff.index', compact('staff'));
    }

    public function createStaff()
    {
        $services = Service::where('status', true)->get();
        return view('admin.staff.create', compact('services'));
    }

    public function storeStaff(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'bio' => 'nullable|string|max:1000',
            'image' => 'nullable|image|max:1024', // max 1MB
            'status' => 'boolean',
            'services' => 'nullable|array',
            'services.*' => 'exists:services,id',
        ]);

        $status = $request->has('status');

        $specialistData = [
            'name' => $validated['name'],
            'bio' => $validated['bio'] ?? null,
            'status' => $status,
        ];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('specialists', 'public');
            $specialistData['image_path'] = $path;
        }

        $specialist = Specialist::create($specialistData);

        if (!empty($validated['services'])) {
            $specialist->services()->sync($validated['services']);
        }

        return redirect()->route('admin.staff.index')->with('success', 'Staff member added successfully');
    }

    public function editStaff(Specialist $specialist)
    {
        $services = Service::where('status', true)->get();
        $specialist->load('services');
        return view('admin.staff.edit', compact('specialist', 'services'));
    }

    public function updateStaff(Request $request, Specialist $specialist)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'bio' => 'nullable|string|max:1000',
            'image' => 'nullable|image|max:1024',
            'status' => 'boolean',
            'services' => 'nullable|array',
            'services.*' => 'exists:services,id',
        ]);

        $status = $request->has('status');

        $specialistData = [
            'name' => $validated['name'],
            'bio' => $validated['bio'] ?? null,
            'status' => $status,
        ];

        if ($request->hasFile('image')) {
            // Delete old image
            if ($specialist->image_path) {
                Storage::disk('public')->delete($specialist->image_path);
            }
            $path = $request->file('image')->store('specialists', 'public');
            $specialistData['image_path'] = $path;
        }

        $specialist->update($specialistData);

        // Sync services
        $services = $validated['services'] ?? [];
        $specialist->services()->sync($services);

        return redirect()->route('admin.staff.index')->with('success', 'Staff member updated successfully');
    }

    public function destroyStaff(Specialist $specialist)
    {
        if ($specialist->image_path) {
            Storage::disk('public')->delete($specialist->image_path);
        }
        $specialist->delete();
        return redirect()->route('admin.staff.index')->with('success', 'Staff member deleted successfully');
    }
}
