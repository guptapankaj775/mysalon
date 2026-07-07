<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use App\Models\Specialist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ServiceCategory;
use App\Models\Inventory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $isAdmin = Auth::user()->isAdmin();

        // Get today's bookings count
        $todayBookingsQuery = Booking::whereDate('appointment_date', today());
        if (!$isAdmin) {
            $todayBookingsQuery->whereHas('service', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }
        $todayBookings = $todayBookingsQuery->count();

        // Get total revenue
        $totalRevenueQuery = Booking::where('payment_status', 'paid');
        if (!$isAdmin) {
            $totalRevenueQuery->whereHas('service', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }
        $totalRevenue = $totalRevenueQuery->sum('total_price');

        // Get total active services
        $totalServicesQuery = Service::query();
        if (!$isAdmin) {
            $totalServicesQuery->where('user_id', $userId);
        }
        $totalServices = $totalServicesQuery->count();

        // Get total customers (unique customers from bookings)
        $totalCustomersQuery = Booking::distinct('email');
        if (!$isAdmin) {
            $totalCustomersQuery->whereHas('service', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }
        $totalCustomers = $totalCustomersQuery->count('email');

        // Get recent bookings
        $recentBookingsQuery = Booking::with(['service', 'category']);
        if (!$isAdmin) {
            $recentBookingsQuery->whereHas('service', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }
        $recentBookings = $recentBookingsQuery->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // Get popular services
        $popularServicesQuery = Service::query()
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
            ->take(5);
        if (!$isAdmin) {
            $popularServicesQuery->where('user_id', $userId);
        }
        $popularServices = $popularServicesQuery->get();

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
        $query = Service::query()
            ->select('services.*')
            ->withCount('bookings')
            ->with(['category', 'icon', 'inventories'])
            ->selectSub(function ($query) {
                $query->from('bookings')
                    ->selectRaw('COALESCE(SUM(total_price), 0)')
                    ->whereColumn('bookings.service_id', 'services.id')
                    ->where('payment_status', 'paid');
            }, 'revenue');

        if (!Auth::user()->isAdmin()) {
            $query->where('user_id', Auth::id());
        }

        $services = $query->orderBy('services.name')
            ->get();

        return view('admin.services.index', compact('services'));
    }

    public function createService()
    {
        $categoriesQuery = ServiceCategory::query();
        $inventoriesQuery = Inventory::orderBy('item_name');

        if (!Auth::user()->isAdmin()) {
            $categoriesQuery->where('user_id', Auth::id());
            $inventoriesQuery->where('user_id', Auth::id());
        }

        $categories = $categoriesQuery->get();
        $inventories = $inventoriesQuery->get();
        return view('admin.services.create', compact('categories', 'inventories'));
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
            'icon' => 'required|string|max:50',
            'inventories' => 'nullable|array',
            'inventories.*' => 'exists:inventories,id',
        ]);

        $iconPath = $validated['icon'];
        unset($validated['icon']);
        
        $inventoriesInput = $request->input('inventories', []);
        unset($validated['inventories']);

        $validated['user_id'] = Auth::id();
        $service = Service::create($validated);
        $service->icon()->create(['image_path' => $iconPath]);

        if (!empty($inventoriesInput)) {
            $service->inventories()->sync($inventoriesInput);
        }

        return redirect()->route('admin.services')->with('success', 'Service created successfully');
    }

    public function editService(Service $service)
    {
        if (!Auth::user()->isAdmin() && $service->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this service.');
        }

        $categoriesQuery = ServiceCategory::query();
        $inventoriesQuery = Inventory::orderBy('item_name');

        if (!Auth::user()->isAdmin()) {
            $categoriesQuery->where('user_id', Auth::id());
            $inventoriesQuery->where('user_id', Auth::id());
        }

        $categories = $categoriesQuery->get();
        $inventories = $inventoriesQuery->get();
        $service->load('inventories');
        return view('admin.services.edit', compact('service', 'categories', 'inventories'));
    }

    public function updateService(Request $request, Service $service)
    {
        if (!Auth::user()->isAdmin() && $service->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this service.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'features' => 'nullable|array',
            'duration' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:service_categories,id',
            'status' => 'boolean',
            'icon' => 'required|string|max:50',
            'inventories' => 'nullable|array',
            'inventories.*' => 'exists:inventories,id',
        ]);

        $iconPath = $validated['icon'];
        unset($validated['icon']);

        $inventoriesInput = $request->input('inventories', []);
        unset($validated['inventories']);

        $service->update($validated);

        // Update or create icon
        if ($service->icon) {
            $service->icon->update(['image_path' => $iconPath]);
        } else {
            $service->icon()->create(['image_path' => $iconPath]);
        }

        $service->inventories()->sync($inventoriesInput);

        return redirect()->route('admin.services')->with('success', 'Service updated successfully');
    }

    public function destroyService(Service $service)
    {
        if (!Auth::user()->isAdmin() && $service->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this service.');
        }

        $service->delete();
        return redirect()->route('admin.services')->with('success', 'Service deleted successfully');
    }

    public function bookings(Request $request)
    {
        $query = Booking::with(['service', 'category']);

        if (!Auth::user()->isAdmin()) {
            $query->whereHas('service', function ($q) {
                $q->where('user_id', Auth::id());
            });
        }

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
        if (!Auth::user()->isAdmin() && $booking->service->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this booking.');
        }

        $booking->load(['service', 'category']);
        return view('admin.bookings.show', compact('booking'));
    }

    public function updateBookingStatus(Request $request, Booking $booking)
    {
        if (!Auth::user()->isAdmin() && $booking->service->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this booking.');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed',
            'payment_status' => 'required|in:pending,paid,failed'
        ]);

        $booking->update($validated);
        return redirect()->back()->with('success', 'Booking status updated successfully');
    }

    public function confirmBooking(Booking $booking)
    {
        if (!Auth::user()->isAdmin() && $booking->service->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this booking.');
        }

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
        if (!Auth::user()->isAdmin() && $booking->service->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this booking.');
        }

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
        if (!Auth::user()->isAdmin() && $booking->service->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this booking.');
        }

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
        if (!Auth::user()->isAdmin() && $booking->service->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this booking.');
        }

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
        $query = User::query();
        if (!Auth::user()->isAdmin()) {
            $query->where('created_by', Auth::id());
        }
        $users = $query->orderBy('created_at', 'desc')->paginate(10);
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
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function editUser(User $user)
    {
        if (!Auth::user()->isAdmin() && $user->created_by !== Auth::id()) {
            abort(403, 'Unauthorized access to this user.');
        }
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        if (!Auth::user()->isAdmin() && $user->created_by !== Auth::id()) {
            abort(403, 'Unauthorized access to this user.');
        }

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
        if (!Auth::user()->isAdmin() && $user->created_by !== Auth::id()) {
            abort(403, 'Unauthorized access to this user.');
        }

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
        if (!Auth::user()->isAdmin() && $user->created_by !== Auth::id()) {
            abort(403, 'Unauthorized access to this user.');
        }

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
        $query = Specialist::with('services');
        if (!Auth::user()->isAdmin()) {
            $query->where('user_id', Auth::id());
        }
        $staff = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.staff.index', compact('staff'));
    }

    public function createStaff()
    {
        $servicesQuery = Service::where('status', true);
        if (!Auth::user()->isAdmin()) {
            $servicesQuery->where('user_id', Auth::id());
        }
        $services = $servicesQuery->get();
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
            'user_id' => Auth::id(),
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
        if (!Auth::user()->isAdmin() && $specialist->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this staff member.');
        }

        $servicesQuery = Service::where('status', true);
        if (!Auth::user()->isAdmin()) {
            $servicesQuery->where('user_id', Auth::id());
        }
        $services = $servicesQuery->get();

        $specialist->load('services');
        return view('admin.staff.edit', compact('specialist', 'services'));
    }

    public function updateStaff(Request $request, Specialist $specialist)
    {
        if (!Auth::user()->isAdmin() && $specialist->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this staff member.');
        }

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
        if (!Auth::user()->isAdmin() && $specialist->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this staff member.');
        }

        if ($specialist->image_path) {
            Storage::disk('public')->delete($specialist->image_path);
        }
        $specialist->delete();
        return redirect()->route('admin.staff.index')->with('success', 'Staff member deleted successfully');
    }
}
