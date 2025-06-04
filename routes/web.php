<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\pages\HomeController;
use App\Http\Controllers\pages\VenueController;
use App\Http\Controllers\pages\BookingController;
use App\Http\Controllers\pages\BookingHistoryController;
use App\Http\Controllers\admin\BookingsController;
use App\Http\Controllers\admin\TableController;
use App\Http\Controllers\admin\RevenueController;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\VenueController as AdminVenueController; // Import admin venue controller
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\superadmin\SuperAdminController;
use App\Http\Controllers\superadmin\AdminManagementController;
use App\Http\Controllers\superadmin\VenueManagementController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Authentication Routes (dengan verifikasi email aktif)
Auth::routes(['verify' => true]);

// Rute custom untuk verifikasi email
Route::get('/email/verified', [VerificationController::class, 'verified'])->name('verification.verified');

Route::get('/', [HomeController::class, "index"])->name('home');
Route::get('/venue/{venueName}', [VenueController::class, "venue"])->name('venue');

// Changed routes for the new booking flow
Route::post('/booking/initiate', [BookingController::class, 'createPaymentIntent'])->name('booking.initiate');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
Route::get('/booking/schedules', [BookingController::class, 'getBookedSchedules'])->name('booking.schedules');
Route::post('/payment/notification', [BookingController::class, 'handleNotification'])->name('payment.notification');

// Routes that require authentication but not email verification
Route::middleware(['auth'])->group(function () {
    // Route untuk melihat halaman verifikasi email dan kirim ulang email verifikasi
    // sudah ditangani oleh Auth::routes(['verify' => true])
    
    // Profile route access without verification
    Route::get('/profile', [AccountController::class, 'settings'])->name('profile');
    
    // Account settings routes - moved from password confirmation middleware
    Route::get('/account/settings', [AccountController::class, 'settings'])->name('account.settings');
    Route::put('/account/update', [AccountController::class, 'update'])->name('account.update');
});

// Routes that require both authentication and email verification
Route::middleware(['auth', 'verified'])->group(function () {
     // Admin direct booking route (hanya akan berfungsi untuk admin dari controller)
    Route::post('/booking/admin-direct', [BookingController::class, 'adminDirectBooking'])->name('booking.admin-direct');
    
    // Booking history routes
    Route::get('/booking/history', [BookingHistoryController::class, 'index'])->name('booking.history');
    
    // Pending bookings routes
    Route::get('/booking/pending', [BookingController::class, 'getPendingBookings'])->name('booking.pending');
    Route::get('/booking/pending/{id}/resume', [BookingController::class, 'resumeBooking'])->name('booking.resume');
    Route::delete('/booking/pending/{id}', [BookingController::class, 'deletePendingBooking'])->name('booking.pending.delete');
    
    // Route Reschedule
    Route::get('/booking/{id}/reschedule', [BookingController::class, 'showReschedule'])->name('booking.reschedule.form');
    Route::post('/booking/{id}/reschedule', [BookingController::class, 'processReschedule'])->name('booking.reschedule.process');
    Route::get('/booking/reschedule/check-availability', [BookingController::class, 'checkRescheduleAvailability'])->name('booking.reschedule.check-availability');

    // Routes that require password confirmation - moved account settings out of this group
    Route::middleware(['password.confirm'])->group(function () {
        // Any sensitive operations that should still require password confirmation can go here
    });

});

// Admin routes (admin tetap perlu verified untuk keamanan)
Route::middleware(['auth', 'verified', 'is_admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
    
    // Booking management routes
    Route::get('/bookings', [BookingsController::class, 'index'])->name('admin.bookings.index');
    Route::get('/bookings/export', [BookingsController::class, 'export'])->name('admin.bookings.export');
    Route::get('/bookings/{id}', [BookingsController::class, 'show'])->name('admin.bookings.show');
    Route::get('/bookings/{id}/edit', [BookingsController::class, 'edit'])->name('admin.bookings.edit');
    Route::put('/bookings/{id}', [BookingsController::class, 'update'])->name('admin.bookings.update');
    Route::patch('/bookings/{id}/complete', [BookingsController::class, 'complete'])->name('admin.bookings.complete');
    Route::patch('/bookings/{id}/cancel', [BookingsController::class, 'cancel'])->name('admin.bookings.cancel');
    
    // Table management routes
    Route::get('/tables', [TableController::class, 'index'])->name('admin.tables.index');
    Route::get('/tables/create', [TableController::class, 'create'])->name('admin.tables.create');
    Route::post('/tables', [TableController::class, 'store'])->name('admin.tables.store');
    Route::get('/tables/{id}/edit', [TableController::class, 'edit'])->name('admin.tables.edit');
    Route::put('/tables/{id}', [TableController::class, 'update'])->name('admin.tables.update');
    Route::delete('/tables/{id}', [TableController::class, 'destroy'])->name('admin.tables.destroy');

    // Venue management routes
    Route::get('/venue', [AdminVenueController::class, 'index'])->name('admin.venue.index');
    Route::get('/venue/edit', [AdminVenueController::class, 'edit'])->name('admin.venue.edit');
    Route::put('/venue/update', [AdminVenueController::class, 'update'])->name('admin.venue.update');

    // Revenue management routes
    Route::get('/revenues', [RevenueController::class, 'index'])->name('admin.revenues.index');
    Route::get('/revenues/detail/{tableId}', [RevenueController::class, 'detail'])->name('admin.revenues.detail');
    Route::get('/revenues/export', [RevenueController::class, 'export'])->name('admin.revenues.export');
});

// Superadmin routes
Route::middleware(['auth', 'verified', 'is_superadmin'])->prefix('superadmin')->group(function () {
    Route::get('/', [App\Http\Controllers\superadmin\SuperAdminController::class, 'index'])->name('superadmin.dashboard');

    // Admin management routes
    Route::get('/admin', [AdminManagementController::class, 'index'])->name('superadmin.admin.index');
    Route::get('/admin/create', [AdminManagementController::class, 'create'])->name('superadmin.admin.create');
    Route::post('/admin', [AdminManagementController::class, 'store'])->name('superadmin.admin.store');
    Route::get('/admin/{id}/edit', [AdminManagementController::class, 'edit'])->name('superadmin.admin.edit');
    Route::put('/admin/{id}', [AdminManagementController::class, 'update'])->name('superadmin.admin.update');
    Route::delete('/admin/{id}', [AdminManagementController::class, 'destroy'])->name('superadmin.admin.destroy');
    
    // Venue management routes (for superadmin)
    Route::get('/venue', [VenueManagementController::class, 'index'])->name('superadmin.venue.index');
    Route::get('/venue/create', [VenueManagementController::class, 'create'])->name('superadmin.venue.create');
    Route::post('/venue', [VenueManagementController::class, 'store'])->name('superadmin.venue.store');
    Route::get('/venue/{id}/edit', [VenueManagementController::class, 'edit'])->name('superadmin.venue.edit');
    Route::put('/venue/{id}', [VenueManagementController::class, 'update'])->name('superadmin.venue.update');
    Route::delete('/venue/{id}', [VenueManagementController::class, 'destroy'])->name('superadmin.venue.destroy');
});