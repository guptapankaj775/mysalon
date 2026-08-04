<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/services', [ServiceController::class, 'index'])->name('services');
Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');

    // Subscription routes
    Route::get('/subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::post('/subscription/select', [SubscriptionController::class, 'selectPlan'])->name('subscription.select');
    Route::get('/subscription/{subscription}/payment', [SubscriptionController::class, 'payment'])->name('subscription.payment');
    Route::post('/subscription/{subscription}/payment', [SubscriptionController::class, 'processPayment'])->name('subscription.payment.process');
    Route::get('/subscription/{subscription}/success', [SubscriptionController::class, 'success'])->name('subscription.success');

    // Feedback routes
    Route::get('/feedback/create/{booking}', [FeedbackController::class, 'create'])->name('feedback.create');
    Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');

    // Admin routes
    // Admin routes closure
    $adminRoutes = function () {
        Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');

        // Booking creation routes for admin/staff
        Route::middleware('can:create_bookings')->group(function () {
            Route::get('/services/book', [AdminController::class, 'bookServices'])->name('admin.services.book');
            Route::get('/bookings/create', [AdminController::class, 'createBooking'])->name('admin.bookings.create');
            Route::post('/bookings/store', [AdminController::class, 'storeBooking'])->name('admin.bookings.store');
            Route::get('/bookings/check-staff-availability', [AdminController::class, 'checkStaffAvailability'])->name('admin.bookings.check-staff-availability');
            Route::get('/bookings/available-slots', [AdminController::class, 'getAvailableSlots'])->name('admin.bookings.available-slots');
        });

        // Bookings routes
        Route::middleware('can:manage_bookings')->group(function () {
            Route::get('/bookings', [AdminController::class, 'bookings'])->name('admin.bookings');
            Route::post('/bookings/{booking}/confirm', [AdminController::class, 'confirmBooking'])->name('admin.bookings.confirm');
            Route::post('/bookings/{booking}/reject', [AdminController::class, 'rejectBooking'])->name('admin.bookings.reject');
            Route::post('/bookings/{booking}/cancel', [AdminController::class, 'cancelBooking'])->name('admin.bookings.cancel');
            Route::post('/bookings/{booking}/complete', [AdminController::class, 'completeBooking'])->name('admin.bookings.complete');
            Route::get('/bookings/{booking}', [AdminController::class, 'showBooking'])->name('admin.bookings.show');
            
            // Staff service workflow
            Route::post('/bookings/services/{id}/start', [AdminController::class, 'startService'])->name('admin.bookings.services.start');
            Route::post('/bookings/services/{id}/complete', [AdminController::class, 'completeService'])->name('admin.bookings.services.complete');
            Route::post('/bookings/{booking}/services/start', [AdminController::class, 'startBookingServices'])->name('admin.bookings.all-services.start');
            Route::post('/bookings/{booking}/services/complete', [AdminController::class, 'completeBookingServices'])->name('admin.bookings.all-services.complete');
            Route::get('/reports/sales', [ReportController::class, 'salesReport'])->name('admin.reports.sales')->middleware('can:view_sales_reports');
        });

        // Services & Categories routes
        Route::middleware('can:manage_services')->group(function () {
            Route::get('/services', [AdminController::class, 'services'])->name('admin.services');
            Route::get('/services/create', [AdminController::class, 'createService'])->name('admin.services.create');
            Route::post('/services', [AdminController::class, 'storeService'])->name('admin.services.store');
            Route::get('/services/{service}/edit', [AdminController::class, 'editService'])->name('admin.services.edit');
            Route::put('/services/{service}', [AdminController::class, 'updateService'])->name('admin.services.update');
            Route::delete('/services/{service}', [AdminController::class, 'destroyService'])->name('admin.services.destroy');

            Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories');
            Route::get('/categories/create', [CategoryController::class, 'create'])->name('admin.categories.create');
            Route::post('/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
            Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('admin.categories.edit');
            Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('admin.categories.update');
            Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');
        });

        // Feedback management routes
        Route::middleware('can:manage_feedbacks')->group(function () {
            Route::get('/feedbacks', [App\Http\Controllers\Admin\FeedbackController::class, 'index'])->name('admin.feedback.index');
            Route::patch('/feedbacks/{feedback}/toggle-publish', [App\Http\Controllers\Admin\FeedbackController::class, 'togglePublish'])->name('admin.feedback.toggle-publish');
        });

        // Users routes
        Route::middleware('can:manage_users')->group(function () {
            Route::get('/users', [AdminController::class, 'users'])->name('admin.users.index');
            Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
            Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
            Route::patch('/users/{user}/verification', [AdminController::class, 'toggleUserVerification'])->name('admin.users.toggle-verification');
            Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
            Route::get('/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
            Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
        });

        // Staff (Specialist) Management routes
        Route::middleware('can:manage_staff')->group(function () {
            Route::get('/staff', [AdminController::class, 'staff'])->name('admin.staff.index');
            Route::get('/staff/create', [AdminController::class, 'createStaff'])->name('admin.staff.create');
            Route::post('/staff', [AdminController::class, 'storeStaff'])->name('admin.staff.store');
            Route::get('/staff/{specialist}/edit', [AdminController::class, 'editStaff'])->name('admin.staff.edit');
            Route::put('/staff/{specialist}', [AdminController::class, 'updateStaff'])->name('admin.staff.update');
            Route::delete('/staff/{specialist}', [AdminController::class, 'destroyStaff'])->name('admin.staff.destroy');
        });

        // Inventory routes
        Route::middleware('can:manage_inventory')->group(function () {
            Route::get('/inventory', [App\Http\Controllers\Admin\InventoryController::class, 'index'])->name('admin.inventory.index');
            Route::get('/inventory/create', [App\Http\Controllers\Admin\InventoryController::class, 'create'])->name('admin.inventory.create');
            Route::post('/inventory', [App\Http\Controllers\Admin\InventoryController::class, 'store'])->name('admin.inventory.store');
            Route::get('/inventory/{inventory}/edit', [App\Http\Controllers\Admin\InventoryController::class, 'edit'])->name('admin.inventory.edit');
            Route::put('/inventory/{inventory}', [App\Http\Controllers\Admin\InventoryController::class, 'update'])->name('admin.inventory.update');
            Route::delete('/inventory/{inventory}', [App\Http\Controllers\Admin\InventoryController::class, 'destroy'])->name('admin.inventory.destroy');

            Route::resource('brands', App\Http\Controllers\Admin\BrandController::class)->names('admin.brands');
            Route::resource('inventory-categories', App\Http\Controllers\Admin\InventoryCategoryController::class)->names('admin.inventory-categories');
        });

        // Vendors routes
        Route::middleware('can:manage_vendors')->group(function () {
            Route::resource('vendors', App\Http\Controllers\Admin\VendorController::class)->names('admin.vendors');
        });

        // Roles & Permissions & Subscriptions routes
        Route::middleware('can:manage_roles')->group(function () {
            Route::get('/roles-permissions', [App\Http\Controllers\Admin\RolePermissionController::class, 'index'])->name('admin.roles.index');
            Route::post('/roles-permissions', [App\Http\Controllers\Admin\RolePermissionController::class, 'update'])->name('admin.roles.update');

            // Subscription Plans routes
            Route::get('/subscriptions', [App\Http\Controllers\Admin\SubscriptionController::class, 'index'])->name('admin.subscriptions.index');
            Route::get('/subscriptions/create', [App\Http\Controllers\Admin\SubscriptionController::class, 'create'])->name('admin.subscriptions.create');
            Route::post('/subscriptions', [App\Http\Controllers\Admin\SubscriptionController::class, 'store'])->name('admin.subscriptions.store');
            Route::get('/subscriptions/{subscription}/edit', [App\Http\Controllers\Admin\SubscriptionController::class, 'edit'])->name('admin.subscriptions.edit');
            Route::put('/subscriptions/{subscription}', [App\Http\Controllers\Admin\SubscriptionController::class, 'update'])->name('admin.subscriptions.update');
            Route::delete('/subscriptions/{subscription}', [App\Http\Controllers\Admin\SubscriptionController::class, 'destroy'])->name('admin.subscriptions.destroy');

            // Subscribers list & settings
            Route::get('/subscribers', [App\Http\Controllers\Admin\SubscriptionController::class, 'subscribers'])->name('admin.subscribers');
            Route::patch('/subscribers/{userSubscription}/status', [App\Http\Controllers\Admin\SubscriptionController::class, 'updateSubscriptionStatus'])->name('admin.subscribers.update-status');
            Route::get('/subscription-settings', [App\Http\Controllers\Admin\SubscriptionController::class, 'settings'])->name('admin.subscription.settings');
            Route::post('/subscription-settings', [App\Http\Controllers\Admin\SubscriptionController::class, 'updateSettings'])->name('admin.subscription.settings.update');
        });
    };

    // 1. Salon Scoped Admin Group (Merchants)
    Route::middleware(['salon', 'auth', 'admin'])->prefix('{salon}/portal')->where(['salon' => '(?!admin$|login$|register$|forgot-password$|reset-password$|logout$|profile$|subscription$)[a-zA-Z0-9\-]+'])->group($adminRoutes);
    Route::middleware(['salon', 'auth', 'admin'])->prefix('{salon}/admin')->where(['salon' => '(?!admin$|login$|register$|forgot-password$|reset-password$|logout$|profile$|subscription$)[a-zA-Z0-9\-]+'])->group($adminRoutes);

    // 2. Global Admin Group (fallback and Super Admin)
    Route::middleware(['auth', 'admin'])->prefix('admin')->group($adminRoutes);

    // Booking routes
    Route::get('/booking', [BookingController::class, 'index'])->name('booking');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');

    // Payment routes
    Route::get('/booking/{id}/payment', [BookingController::class, 'showPayment'])->name('booking.payment');
    Route::post('/booking/{id}/payment', [BookingController::class, 'processPayment'])->name('booking.payment.process');
    Route::get('/booking/{id}/payment/success', [BookingController::class, 'paymentSuccess'])->name('booking.payment.success');
    Route::get('/booking/{id}/invoice', [BookingController::class, 'showInvoice'])->name('booking.invoice');

    // Booking management routes
    Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/bookings/{id}/reschedule', [BookingController::class, 'reschedule'])->name('bookings.reschedule');
});

// Dedicated Super Admin Login Routes (Guest Only - admin@salonjc.com)
Route::middleware('guest')->group(function () {
    Route::get('/superadmin/login', [App\Http\Controllers\Auth\AdminLoginController::class, 'create'])->name('superadmin.login');
    Route::post('/superadmin/login', [App\Http\Controllers\Auth\AdminLoginController::class, 'store']);
    Route::get('/admin/login', [App\Http\Controllers\Auth\AdminLoginController::class, 'create'])->name('admin.login');
    Route::post('/admin/login', [App\Http\Controllers\Auth\AdminLoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

// Salon-specific scoped routes
Route::middleware(['salon'])->prefix('{salon}')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('salon.home');
    Route::get('/about', [AboutController::class, 'index'])->name('salon.about');
    Route::get('/services', [ServiceController::class, 'index'])->name('salon.services');
    Route::get('/reviews', [ReviewController::class, 'index'])->name('salon.reviews');

    // Salon Scoped Auth Routes
    Route::middleware('guest')->group(function () {
        Route::get('register', [RegisteredUserController::class, 'create'])->name('salon.register');
        Route::post('register', [RegisteredUserController::class, 'store']);
        Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('salon.login');
        Route::post('login', [AuthenticatedSessionController::class, 'store']);
        Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('salon.password.request');
        Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('salon.password.email');
        Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('salon.password.reset');
        Route::post('reset-password', [NewPasswordController::class, 'store'])->name('salon.password.store');
    });

    Route::middleware(['auth'])->group(function () {
        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('salon.logout');
        
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('salon.dashboard');


        // Salon Scoped Subscription routes
        Route::get('/subscription', [SubscriptionController::class, 'index'])->name('salon.subscription.index');
        Route::post('/subscription/select', [SubscriptionController::class, 'selectPlan'])->name('salon.subscription.select');
        Route::get('/subscription/{subscription}/payment', [SubscriptionController::class, 'payment'])->name('salon.subscription.payment');
        Route::post('/subscription/{subscription}/payment', [SubscriptionController::class, 'processPayment'])->name('salon.subscription.payment.process');
        Route::get('/subscription/{subscription}/success', [SubscriptionController::class, 'success'])->name('salon.subscription.success');
        
        // Booking routes
        Route::get('/booking', [BookingController::class, 'index'])->name('salon.booking');
        Route::post('/bookings', [BookingController::class, 'store'])->name('salon.bookings.store');

        // Payment routes
        Route::get('/booking/{id}/payment', [BookingController::class, 'showPayment'])->name('salon.booking.payment');
        Route::post('/booking/{id}/payment', [BookingController::class, 'processPayment'])->name('salon.booking.payment.process');
        Route::get('/booking/{id}/payment/success', [BookingController::class, 'paymentSuccess'])->name('salon.booking.payment.success');
        Route::get('/booking/{id}/invoice', [BookingController::class, 'showInvoice'])->name('salon.booking.invoice');

        // Booking management routes
        Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel'])->name('salon.bookings.cancel');
        Route::post('/bookings/{id}/reschedule', [BookingController::class, 'reschedule'])->name('salon.bookings.reschedule');
    });
});
