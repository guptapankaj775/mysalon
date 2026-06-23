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
    Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');

        // Bookings routes
        Route::get('/bookings', [AdminController::class, 'bookings'])->name('admin.bookings');
        Route::post('/bookings/{booking}/confirm', [AdminController::class, 'confirmBooking'])->name('admin.bookings.confirm');
        Route::post('/bookings/{booking}/reject', [AdminController::class, 'rejectBooking'])->name('admin.bookings.reject');
        Route::post('/bookings/{booking}/cancel', [AdminController::class, 'cancelBooking'])->name('admin.bookings.cancel');
        Route::post('/bookings/{booking}/complete', [AdminController::class, 'completeBooking'])->name('admin.bookings.complete');
        Route::get('/bookings/{booking}', [AdminController::class, 'showBooking'])->name('admin.bookings.show');
        Route::put('/bookings/{booking}/status', [AdminController::class, 'updateBookingStatus'])->name('admin.bookings.update-status');

        // Services routes
        Route::get('/services', [AdminController::class, 'services'])->name('admin.services');
        Route::get('/services/create', [AdminController::class, 'createService'])->name('admin.services.create');
        Route::post('/services', [AdminController::class, 'storeService'])->name('admin.services.store');
        Route::get('/services/{service}/edit', [AdminController::class, 'editService'])->name('admin.services.edit');
        Route::put('/services/{service}', [AdminController::class, 'updateService'])->name('admin.services.update');
        Route::delete('/services/{service}', [AdminController::class, 'destroyService'])->name('admin.services.destroy');

        // Feedback management routes
        Route::get('/feedbacks', [App\Http\Controllers\Admin\FeedbackController::class, 'index'])->name('admin.feedback.index');
        Route::patch('/feedbacks/{feedback}/toggle-publish', [App\Http\Controllers\Admin\FeedbackController::class, 'togglePublish'])->name('admin.feedback.toggle-publish');

        // Users routes
        Route::get('/users', [AdminController::class, 'users'])->name('admin.users.index');
        Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
        Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
        Route::patch('/users/{user}/verification', [AdminController::class, 'toggleUserVerification'])->name('admin.users.toggle-verification');
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('admin.users.destroy');
        Route::get('/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
        Route::post('/users', [AdminController::class, 'storeUser'])->name('admin.users.store');

        // Staff (Specialist) Management routes
        Route::get('/staff', [AdminController::class, 'staff'])->name('admin.staff.index');
        // Let's create specific routes to avoid model-binding conflicts or matching order issues
        Route::get('/staff/create', [AdminController::class, 'createStaff'])->name('admin.staff.create');
        Route::post('/staff', [AdminController::class, 'storeStaff'])->name('admin.staff.store');
        Route::get('/staff/{specialist}/edit', [AdminController::class, 'editStaff'])->name('admin.staff.edit');
        Route::put('/staff/{specialist}', [AdminController::class, 'updateStaff'])->name('admin.staff.update');
        Route::delete('/staff/{specialist}', [AdminController::class, 'destroyStaff'])->name('admin.staff.destroy');
        
        // Categories routes
        Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories');
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('admin.categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('admin.categories.edit');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('admin.categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');

        // Inventory routes
        Route::get('/inventory', [App\Http\Controllers\Admin\InventoryController::class, 'index'])->name('admin.inventory.index');
        Route::get('/inventory/create', [App\Http\Controllers\Admin\InventoryController::class, 'create'])->name('admin.inventory.create');
        Route::post('/inventory', [App\Http\Controllers\Admin\InventoryController::class, 'store'])->name('admin.inventory.store');
        Route::get('/inventory/{inventory}/edit', [App\Http\Controllers\Admin\InventoryController::class, 'edit'])->name('admin.inventory.edit');
        Route::put('/inventory/{inventory}', [App\Http\Controllers\Admin\InventoryController::class, 'update'])->name('admin.inventory.update');
        Route::delete('/inventory/{inventory}', [App\Http\Controllers\Admin\InventoryController::class, 'destroy'])->name('admin.inventory.destroy');

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

        // Roles & Permissions routes
        Route::get('/roles-permissions', [App\Http\Controllers\Admin\RolePermissionController::class, 'index'])->name('admin.roles.index');
        Route::post('/roles-permissions', [App\Http\Controllers\Admin\RolePermissionController::class, 'update'])->name('admin.roles.update');
    });

    // Booking routes
    Route::get('/booking', [BookingController::class, 'index'])->name('booking');
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');

    // Payment routes
    Route::get('/booking/{id}/payment', [BookingController::class, 'showPayment'])->name('booking.payment');
    Route::post('/booking/{id}/payment', [BookingController::class, 'processPayment'])->name('booking.payment.process');
    Route::get('/booking/{id}/payment/success', [BookingController::class, 'paymentSuccess'])->name('booking.payment.success');

    // Booking management routes
    Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/bookings/{id}/reschedule', [BookingController::class, 'reschedule'])->name('bookings.reschedule');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
