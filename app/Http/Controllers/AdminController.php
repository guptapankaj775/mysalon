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
        if (Auth::user()->role === 'staff') {
            return $this->staffDashboard();
        }

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
            'icon' => 'required',
            'inventories' => 'nullable|array',
            'inventories.*' => 'exists:inventories,id',
        ]);

        $iconPath = null;
        if ($request->hasFile('icon')) {
            $request->validate([
                'icon' => 'image|mimes:svg,png,jpeg,jpg|max:1024'
            ]);
            $iconPath = $request->file('icon')->store('services/icons', 'public');
        } else {
            $request->validate([
                'icon' => 'string|max:255'
            ]);
            $iconPath = $request->input('icon');
        }

        unset($validated['icon']);
        
        $inventoriesInput = $request->input('inventories', []);
        $inventoryQtyInput = $request->input('inventory_qty', []);
        unset($validated['inventories'], $validated['inventory_qty']);

        $validated['user_id'] = Auth::id();
        $service = Service::create($validated);
        $service->icon()->create(['image_path' => $iconPath]);

        $syncData = [];
        foreach ($inventoriesInput as $invId) {
            $qty = isset($inventoryQtyInput[$invId]) && is_numeric($inventoryQtyInput[$invId]) && (float)$inventoryQtyInput[$invId] > 0
                ? (float)$inventoryQtyInput[$invId]
                : 1.00;
            $syncData[$invId] = ['quantity' => $qty];
        }
        $service->inventories()->sync($syncData);

        return redirect()->route('admin.services')->with('success', 'Service created successfully');
    }

    public function editService(Service $service)
    {
        if (!Auth::user()->isAdmin() && $service->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this service.');
        }

        $categoriesQuery = ServiceCategory::orderBy('name');
        if (!Auth::user()->isAdmin()) {
            $categoriesQuery->where('user_id', Auth::id());
        }
        $categories = $categoriesQuery->get();

        $inventoriesQuery = Inventory::orderBy('item_name');
        if (!Auth::user()->isAdmin()) {
            $inventoriesQuery->where('user_id', Auth::id());
        }
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
            'features.*' => 'nullable|string|max:255',
            'duration' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:service_categories,id',
            'status' => 'boolean',
            'icon' => 'nullable',
            'inventories' => 'nullable|array',
            'inventories.*' => 'exists:inventories,id',
            'inventory_qty' => 'nullable|array',
        ]);

        $iconPath = null;
        if ($request->hasFile('icon')) {
            $request->validate([
                'icon' => 'image|mimes:svg,png,jpeg,jpg|max:1024'
            ]);
            $iconPath = $request->file('icon')->store('services/icons', 'public');
        } elseif ($request->filled('icon') && is_string($request->input('icon'))) {
            $request->validate([
                'icon' => 'string|max:255'
            ]);
            $iconPath = $request->input('icon');
        }

        unset($validated['icon']);

        $inventoriesInput = $request->input('inventories', []);
        $inventoryQtyInput = $request->input('inventory_qty', []);
        unset($validated['inventories'], $validated['inventory_qty']);

        $service->update($validated);

        // Update or create icon
        if ($iconPath) {
            // Delete old icon file if it exists and is a file path
            if ($service->icon && (str_contains($service->icon->path, '/') || \Illuminate\Support\Str::endsWith($service->icon->path, ['.svg', '.png', '.jpg', '.jpeg']))) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($service->icon->path);
            }

            if ($service->icon) {
                $service->icon->update(['image_path' => $iconPath]);
            } else {
                $service->icon()->create(['image_path' => $iconPath]);
            }
        }

        $syncData = [];
        foreach ($inventoriesInput as $invId) {
            $qty = isset($inventoryQtyInput[$invId]) && is_numeric($inventoryQtyInput[$invId]) && (float)$inventoryQtyInput[$invId] > 0
                ? (float)$inventoryQtyInput[$invId]
                : 1.00;
            $syncData[$invId] = ['quantity' => $qty];
        }
        $service->inventories()->sync($syncData);

        return redirect()->route('admin.services')->with('success', 'Service updated successfully');
    }

    public function destroyService(Service $service)
    {
        if (!Auth::user()->isAdmin() && $service->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this service.');
        }

        // Delete icon file if it exists and is a file path
        if ($service->icon && (str_contains($service->icon->path, '/') || \Illuminate\Support\Str::endsWith($service->icon->path, ['.svg', '.png', '.jpg', '.jpeg']))) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($service->icon->path);
        }

        $service->delete();
        return redirect()->route('admin.services')->with('success', 'Service deleted successfully');
    }

    public function bookings(Request $request)
    {
        $query = Booking::with(['service', 'category']);

        if (!Auth::user()->isAdmin()) {
            $ownerId = Auth::user()->created_by ?: Auth::id();
            $query->whereHas('service', function ($q) use ($ownerId) {
                $q->where('user_id', $ownerId);
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
        if (!Auth::user()->isAdmin() && $booking->service->user_id !== (Auth::user()->created_by ?: Auth::id())) {
            abort(403, 'Unauthorized access to this booking.');
        }

        $booking->load(['service', 'category']);
        return view('admin.bookings.show', compact('booking'));
    }

    public function updateBookingStatus(Request $request, Booking $booking)
    {
        if (!Auth::user()->isAdmin() && $booking->service->user_id !== (Auth::user()->created_by ?: Auth::id())) {
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
        if (!Auth::user()->isAdmin() && $booking->service->user_id !== (Auth::user()->created_by ?: Auth::id())) {
            abort(403, 'Unauthorized access to this booking.');
        }

        if (in_array($booking->status, ['in_progress', 'completed', 'closed'])) {
            return redirect()->back()->with('error', 'Cannot update a booking that has already started.');
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
        if (!Auth::user()->isAdmin() && $booking->service->user_id !== (Auth::user()->created_by ?: Auth::id())) {
            abort(403, 'Unauthorized access to this booking.');
        }

        if (in_array($booking->status, ['in_progress', 'completed', 'closed'])) {
            return redirect()->back()->with('error', 'Cannot update a booking that has already started.');
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
        if (Auth::user()->role === 'staff') {
            abort(403, 'Staff members are not allowed to cancel bookings.');
        }

        if (!Auth::user()->isAdmin() && $booking->service->user_id !== (Auth::user()->created_by ?: Auth::id())) {
            abort(403, 'Unauthorized access to this booking.');
        }

        if (in_array($booking->status, ['in_progress', 'completed', 'closed'])) {
            return redirect()->back()->with('error', 'Cannot update a booking that has already started.');
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
        if (!Auth::user()->isAdmin() && $booking->service->user_id !== (Auth::user()->created_by ?: Auth::id())) {
            abort(403, 'Unauthorized access to this booking.');
        }

        if (in_array($booking->status, ['in_progress', 'completed', 'closed'])) {
            return redirect()->back()->with('error', 'Cannot update a booking that has already started.');
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
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users,email',
            'password'    => 'required|min:8|confirmed',
            'role'        => 'required|in:user,admin,staff',
            'salon_name'  => 'nullable|string|max:255',
            'slug'        => 'nullable|string|max:255|unique:users,slug',
            'phone'       => 'nullable|string|max:20',
            'salon_type'  => 'nullable|string|max:255',
            'salon_model' => 'nullable|string|max:255',
            'address'     => 'nullable|string|max:500',
            'city'        => 'nullable|string|max:100',
            'state'       => 'nullable|string|max:100',
            'zip'         => 'nullable|string|max:20',
            'is_verified' => 'nullable|boolean',
        ]);

        $slug = !empty($validated['slug'])
            ? \Illuminate\Support\Str::slug($validated['slug'])
            : (!empty($validated['salon_name']) ? \Illuminate\Support\Str::slug($validated['salon_name']) : null);

        if ($slug && User::where('slug', $slug)->exists()) {
            $slug = $slug . '-' . time();
        }

        User::create([
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'password'    => Hash::make($validated['password']),
            'role'        => $validated['role'],
            'salon_name'  => $validated['salon_name'] ?? null,
            'slug'        => $slug,
            'phone'       => $validated['phone'] ?? null,
            'salon_type'  => $validated['salon_type'] ?? null,
            'salon_model' => $validated['salon_model'] ?? null,
            'address'     => $validated['address'] ?? null,
            'city'        => $validated['city'] ?? null,
            'state'       => $validated['state'] ?? null,
            'zip'         => $validated['zip'] ?? null,
            'is_verified' => $request->boolean('is_verified'),
            'created_by'  => Auth::id(),
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('user_created', 'Salon / User created successfully.');
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
            'name'        => 'required|string|max:255',
            'email'       => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password'    => 'nullable|string|min:8|confirmed',
            'role'        => 'required|in:user,admin,staff',
            'salon_name'  => 'nullable|string|max:255',
            'slug'        => 'nullable|string|max:255|unique:users,slug,' . $user->id,
            'phone'       => 'nullable|string|max:20',
            'salon_type'  => 'nullable|string|max:255',
            'salon_model' => 'nullable|string|max:255',
            'address'     => 'nullable|string|max:500',
            'city'        => 'nullable|string|max:100',
            'state'       => 'nullable|string|max:100',
            'zip'         => 'nullable|string|max:20',
            'is_verified' => 'nullable|boolean',
        ]);

        $slug = !empty($validated['slug'])
            ? \Illuminate\Support\Str::slug($validated['slug'])
            : (!empty($validated['salon_name']) ? \Illuminate\Support\Str::slug($validated['salon_name']) : $user->slug);

        $updateData = [
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'role'        => $validated['role'],
            'salon_name'  => $validated['salon_name'] ?? null,
            'slug'        => $slug,
            'phone'       => $validated['phone'] ?? null,
            'salon_type'  => $validated['salon_type'] ?? null,
            'salon_model' => $validated['salon_model'] ?? null,
            'address'     => $validated['address'] ?? null,
            'city'        => $validated['city'] ?? null,
            'state'       => $validated['state'] ?? null,
            'zip'         => $validated['zip'] ?? null,
            'is_verified' => $user->id === Auth::id() ? true : $request->boolean('is_verified'),
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = bcrypt($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('admin.users.index')
            ->with('user_updated', 'Salon / User updated successfully.');
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

    private function syncSpecialistsForOwner($ownerId)
    {
        if (!$ownerId) {
            return;
        }

        $staffUsers = \App\Models\User::where('role', 'staff')
            ->where('created_by', $ownerId)
            ->get();

        foreach ($staffUsers as $su) {
            \App\Models\Specialist::firstOrCreate(
                ['email' => $su->email],
                [
                    'name' => $su->name,
                    'user_id' => $ownerId,
                    'status' => true,
                    'mobile_no' => $su->phone,
                ]
            );
        }
    }

    // Staff (Specialist) Management Methods
    public function staff()
    {
        $ownerId = Auth::user()->created_by ?: Auth::id();
        $this->syncSpecialistsForOwner($ownerId);

        $query = Specialist::with('services');
        if (!Auth::user()->isAdmin()) {
            $query->where('user_id', $ownerId);
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

        // Get unique job categories from existing specialists
        $defaultCategories = ['Hair', 'Makeup', 'Pedicure', 'Nail Art'];
        $specialistsQuery = Specialist::query();
        if (!Auth::user()->isAdmin()) {
            $specialistsQuery->where('user_id', Auth::id());
        }
        $existingCategories = $specialistsQuery->whereNotNull('job_category')
            ->get()
            ->pluck('job_category')
            ->flatten()
            ->unique()
            ->filter()
            ->toArray();

        $jobCategories = array_values(array_unique(array_merge($defaultCategories, $existingCategories)));

        return view('admin.staff.create', compact('services', 'jobCategories'));
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
            'mobile_no' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'password' => 'nullable|string|min:8',
            'job_category' => 'nullable|array',
            'job_category.*' => 'string|max:50',
            'home_address' => 'nullable|string|max:500',
            'religion' => 'nullable|string|max:50',
        ]);

        $status = $request->has('status');

        // Create login user if email and password are provided
        if (!empty($validated['email'])) {
            // Verify email doesn't exist
            if (\App\Models\User::where('email', $validated['email'])->exists()) {
                return back()->withErrors(['email' => 'The email has already been taken.'])->withInput();
            }

            if (!empty($validated['password'])) {
                \App\Models\User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['mobile_no'] ?? null,
                    'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
                    'role' => 'staff',
                    'created_by' => Auth::id(),
                    'is_verified' => true,
                    'salon_name' => Auth::user()->salon_name,
                    'slug' => Auth::user()->slug,
                    'salon_type' => Auth::user()->salon_type,
                    'salon_model' => Auth::user()->salon_model,
                ]);
            }
        }

        $specialistData = [
            'name' => $validated['name'],
            'bio' => $validated['bio'] ?? null,
            'status' => $status,
            'user_id' => Auth::id(),
            'mobile_no' => $validated['mobile_no'] ?? null,
            'email' => $validated['email'] ?? null,
            'job_category' => $validated['job_category'] ?? null,
            'home_address' => $validated['home_address'] ?? null,
            'religion' => $validated['religion'] ?? null,
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

        // Get unique job categories from existing specialists
        $defaultCategories = ['Hair', 'Makeup', 'Pedicure', 'Nail Art'];
        $specialistsQuery = Specialist::query();
        if (!Auth::user()->isAdmin()) {
            $specialistsQuery->where('user_id', Auth::id());
        }
        $existingCategories = $specialistsQuery->whereNotNull('job_category')
            ->get()
            ->pluck('job_category')
            ->flatten()
            ->unique()
            ->filter()
            ->toArray();

        $jobCategories = array_values(array_unique(array_merge($defaultCategories, $existingCategories)));

        $specialist->load('services');
        return view('admin.staff.edit', compact('specialist', 'services', 'jobCategories'));
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
            'mobile_no' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'password' => 'nullable|string|min:8',
            'job_category' => 'nullable|array',
            'job_category.*' => 'string|max:50',
            'home_address' => 'nullable|string|max:500',
            'religion' => 'nullable|string|max:50',
        ]);

        $status = $request->has('status');

        $oldEmail = $specialist->email;

        // Find existing login user if any
        $staffUser = null;
        if ($oldEmail) {
            $staffUser = \App\Models\User::where('email', $oldEmail)->where('role', 'staff')->where('created_by', Auth::id())->first();
        }

        if (!empty($validated['email'])) {
            // Verify new email isn't taken by another user
            if ($validated['email'] !== $oldEmail && \App\Models\User::where('email', $validated['email'])->exists()) {
                return back()->withErrors(['email' => 'The email has already been taken.'])->withInput();
            }

            if ($staffUser) {
                $updateData = [
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['mobile_no'] ?? null,
                ];
                if (!empty($validated['password'])) {
                    $updateData['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
                }
                $staffUser->update($updateData);
            } else {
                if (!empty($validated['password'])) {
                    \App\Models\User::create([
                        'name' => $validated['name'],
                        'email' => $validated['email'],
                        'phone' => $validated['mobile_no'] ?? null,
                        'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
                        'role' => 'staff',
                        'created_by' => Auth::id(),
                        'is_verified' => true,
                        'salon_name' => Auth::user()->salon_name,
                        'slug' => Auth::user()->slug,
                        'salon_type' => Auth::user()->salon_type,
                        'salon_model' => Auth::user()->salon_model,
                    ]);
                }
            }
        } else {
            // If email cleared, delete login user
            if ($staffUser) {
                $staffUser->delete();
            }
        }

        $specialistData = [
            'name' => $validated['name'],
            'bio' => $validated['bio'] ?? null,
            'status' => $status,
            'mobile_no' => $validated['mobile_no'] ?? null,
            'email' => $validated['email'] ?? null,
            'job_category' => $validated['job_category'] ?? null,
            'home_address' => $validated['home_address'] ?? null,
            'religion' => $validated['religion'] ?? null,
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

        // Delete corresponding login user if exists
        if ($specialist->email) {
            \App\Models\User::where('email', $specialist->email)->where('role', 'staff')->where('created_by', Auth::id())->delete();
        }

        $specialist->delete();
        return redirect()->route('admin.staff.index')->with('success', 'Staff member deleted successfully');
    }

    public function bookServices()
    {
        \Illuminate\Support\Facades\Gate::authorize('create_bookings');

        $salon = request()->attributes->get('salon');
        $ownerId = $salon ? $salon->id : (Auth::user()->created_by ?: Auth::id());

        $categoriesQuery = \App\Models\ServiceCategory::query()->with(['services' => function ($query) use ($ownerId) {
            $query->where('status', true)->with(['images', 'icon']);
            if ($ownerId) {
                $query->where('user_id', $ownerId);
            }
        }]);

        if ($ownerId) {
            $categoriesQuery->where('user_id', $ownerId);
        }

        $categories = $categoriesQuery->get();

        return view('admin.services.book', compact('categories'));
    }

    public function staffDashboard()
    {
        $staffId = Auth::id();
        $assignedServices = \App\Models\BookingService::with(['booking.user', 'service'])
            ->where('staff_id', $staffId)
            ->whereHas('booking', function ($q) {
                $q->where('status', '!=', 'cancelled');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.staff.dashboard', compact('assignedServices'));
    }

    public function isStaffAvailable($staffId, $date, $time, $durationMinutes, $excludeBookingId = null)
    {
        $durationMinutes = (int) $durationMinutes;
        $start = \Illuminate\Support\Carbon::parse("$date $time");
        $end = (clone $start)->addMinutes($durationMinutes);

        $assignedServices = \App\Models\BookingService::where('staff_id', $staffId)
            ->whereHas('booking', function ($q) use ($date, $excludeBookingId) {
                $q->where('appointment_date', $date)
                  ->where('status', '!=', 'cancelled');
                if ($excludeBookingId) {
                    $q->where('id', '!=', $excludeBookingId);
                }
            })
            ->with(['booking', 'service'])
            ->get();

        foreach ($assignedServices as $bs) {
            $bStart = \Illuminate\Support\Carbon::parse($bs->booking->appointment_date . ' ' . $bs->booking->appointment_time);
            $bEnd = (clone $bStart)->addMinutes($bs->service->duration);

            if ($start < $bEnd && $end > $bStart) {
                return false;
            }
        }

        return true;
    }

    public function checkStaffAvailability(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('create_bookings');

        $request->validate([
            'date' => 'required|date',
            'time' => 'required',
            'duration' => 'required|integer',
            'booking_id' => 'nullable|integer',
        ]);

        $duration = $request->duration;

        $salon = request()->attributes->get('salon');
        $ownerId = $salon ? $salon->id : (Auth::user()->created_by ?: Auth::id());

        $this->syncSpecialistsForOwner($ownerId);

        $staffQuery = \App\Models\User::where('role', 'staff');
        if ($ownerId) {
            $staffQuery->where('created_by', $ownerId);
            $specialistEmails = \App\Models\Specialist::where('user_id', $ownerId)->pluck('email')->filter();
            $staffQuery->whereIn('email', $specialistEmails);
        }
        $allStaff = $staffQuery->get();

        $availableStaff = $allStaff->filter(function ($staff) use ($request, $duration) {
            return $this->isStaffAvailable(
                $staff->id,
                $request->date,
                $request->time,
                $duration,
                $request->booking_id
            );
        })->values();

        return response()->json($availableStaff);
    }

    public function createBooking(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('create_bookings');

        $salon = request()->attributes->get('salon');
        $ownerId = $salon ? $salon->id : (Auth::user()->created_by ?: Auth::id());

        $customers = \App\Models\User::where('role', 'user')->orderBy('name')->get();

        $servicesQuery = \App\Models\Service::where('status', true);
        if ($ownerId) {
            $servicesQuery->where('user_id', $ownerId);
        }
        $allServices = $servicesQuery->get();

        $timeSlots = [];
        $start = new \DateTime('09:00');
        $end = new \DateTime('20:00');
        $interval = new \DateInterval('PT30M');
        $current = clone $start;

        while ($current <= $end) {
            $timeSlots[] = $current->format('H:i');
            $current->add($interval);
        }

        $preselectedServiceId = $request->query('service');

        return view('admin.bookings.create', compact('customers', 'allServices', 'timeSlots', 'preselectedServiceId'));
    }

    public function storeBooking(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('create_bookings');

        $request->validate([
            'customer_type' => 'required|in:existing,new',
            'customer_id' => 'required_if:customer_type,existing|exists:users,id',
            'fullName' => 'required_if:customer_type,new|nullable|string|max:255',
            'phone' => 'required_if:customer_type,new|nullable|string|max:20',
            'email' => 'required_if:customer_type,new|nullable|email',
            'appointmentDate' => 'required|date',
            'appointmentTime' => 'required',
            'staff_id' => 'required|exists:users,id',
            'services' => 'required|array|min:1',
            'services.*.service_id' => 'required|exists:services,id',
        ]);

        if ($request->customer_type === 'new') {
            $existingUser = \App\Models\User::where('email', $request->email)->first();
            if ($existingUser) {
                return redirect()->back()->withInput()->with('error', 'A customer with this email already exists. Please select them from the existing customer list.');
            }

            $customerUser = \App\Models\User::create([
                'name' => $request->fullName,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::random(16)),
                'role' => 'user',
            ]);
        } else {
            $customerUser = \App\Models\User::findOrFail($request->customer_id);
        }

        $staff = \App\Models\User::findOrFail($request->staff_id);

        $selectedServices = [];
        $basePrice = 0;
        $totalDuration = 0;

        foreach ($request->services as $srvData) {
            $service = \App\Models\Service::findOrFail($srvData['service_id']);
            $selectedServices[] = $service;
            $basePrice += $service->price;
            $totalDuration += $service->duration;
        }

        if (!$this->isStaffAvailable($staff->id, $request->appointmentDate, $request->appointmentTime, $totalDuration)) {
            return redirect()->back()->withInput()->with('error', "Staff member {$staff->name} is not available (overlaps with another booking) for the selected services combined duration of {$totalDuration} minutes.");
        }

        $serviceFee = $basePrice * 0.03;
        $totalPrice = $basePrice + $serviceFee;

        $firstService = $selectedServices[0];

        $booking = Booking::create([
            'user_id' => $customerUser->id,
            'full_name' => $customerUser->name,
            'phone' => $customerUser->phone,
            'email' => $customerUser->email,
            'service_category_id' => $firstService->category_id,
            'service_id' => $firstService->id,
            'appointment_date' => $request->appointmentDate,
            'appointment_time' => $request->appointmentTime,
            'stylist_id' => $staff->id,
            'special_requirements' => $request->requirements,
            'base_price' => $basePrice,
            'addons_price' => $serviceFee,
            'total_price' => $totalPrice,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);

        foreach ($selectedServices as $srv) {
            \App\Models\BookingService::create([
                'booking_id' => $booking->id,
                'service_id' => $srv->id,
                'staff_id' => $staff->id,
                'status' => 'assigned',
            ]);
        }

        $booking->updateStatusFromServices();

        return redirect()->route('admin.bookings')->with('success', 'Booking created successfully.');
    }

    public function startService($id)
    {
        $bs = \App\Models\BookingService::findOrFail($id);
        
        if (Auth::user()->role === 'staff' && $bs->staff_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this service.');
        }

        $bs->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        $bs->booking->updateStatusFromServices();

        return redirect()->back()->with('success', 'Service started successfully.');
    }

    public function completeService($id)
    {
        $bs = \App\Models\BookingService::findOrFail($id);
        
        if (Auth::user()->role === 'staff' && $bs->staff_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this service.');
        }

        $bs->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $bs->booking->updateStatusFromServices();

        return redirect()->back()->with('success', 'Service completed successfully.');
    }

    public function startBookingServices(Booking $booking)
    {
        $staffId = Auth::id();

        $services = $booking->bookingServices()
            ->where('staff_id', $staffId)
            ->where('status', 'assigned')
            ->get();

        if ($services->isEmpty()) {
            return redirect()->back()->with('error', 'No assigned services to start for this booking.');
        }

        foreach ($services as $bs) {
            $bs->update([
                'status' => 'in_progress',
                'started_at' => now(),
            ]);
        }

        $booking->updateStatusFromServices();

        return redirect()->back()->with('success', 'Booking services started successfully.');
    }

    public function completeBookingServices(Booking $booking)
    {
        $staffId = Auth::id();

        $services = $booking->bookingServices()
            ->where('staff_id', $staffId)
            ->where('status', 'in_progress')
            ->get();

        if ($services->isEmpty()) {
            return redirect()->back()->with('error', 'No in-progress services to complete for this booking.');
        }

        foreach ($services as $bs) {
            $bs->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);
        }

        $booking->updateStatusFromServices();

        return redirect()->back()->with('success', 'Booking services completed successfully.');
    }

    public function getAvailableSlots(Request $request)
    {
        \Illuminate\Support\Facades\Gate::authorize('create_bookings');

        $request->validate([
            'date' => 'required|date',
            'duration' => 'required|integer',
        ]);

        $date = $request->date;
        $duration = $request->duration;

        $salon = request()->attributes->get('salon');
        $ownerId = $salon ? $salon->id : (Auth::user()->created_by ?: Auth::id());

        $this->syncSpecialistsForOwner($ownerId);

        $staffQuery = \App\Models\User::where('role', 'staff');
        if ($ownerId) {
            $staffQuery->where('created_by', $ownerId);
            $specialistEmails = \App\Models\Specialist::where('user_id', $ownerId)->pluck('email')->filter();
            $staffQuery->whereIn('email', $specialistEmails);
        }
        $allStaff = $staffQuery->get();

        $timeSlots = [];
        $start = new \DateTime('09:00');
        $end = new \DateTime('20:00');
        $interval = new \DateInterval('PT30M');
        $current = clone $start;

        while ($current <= $end) {
            $timeSlots[] = $current->format('H:i');
            $current->add($interval);
        }

        $availableSlots = [];

        foreach ($timeSlots as $slot) {
            foreach ($allStaff as $staff) {
                if ($this->isStaffAvailable($staff->id, $date, $slot, $duration)) {
                    $availableSlots[] = $slot;
                    break;
                }
            }
        }

        return response()->json($availableSlots);
    }
}
