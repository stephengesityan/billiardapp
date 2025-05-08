<?php

use App\Http\Controllers\pages\HomeController;
use App\Http\Controllers\pages\VenueController;
use App\Http\Controllers\pages\BookingController;
use App\Http\Controllers\pages\BookingHistoryController;
use App\Http\Controllers\admin\BookingsController;
use App\Http\Controllers\admin\TableController;
use App\Http\Controllers\admin\AdminController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Auth::routes();
Route::get('/', [HomeController::class, "index"])->name('index');
Route::get('/venue/{venueName}', [VenueController::class, "venue"])->name('venue');

// Changed routes for the new booking flow
Route::post('/booking/payment-intent', [BookingController::class, 'createPaymentIntent'])->name('booking.payment-intent');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
Route::get('/booking/schedules', [BookingController::class, 'getBookedSchedules'])->name('booking.schedules');
Route::post('/payment/notification', [BookingController::class, 'handleNotification'])->name('payment.notification');

// Booking history routes (authenticated only)
Route::middleware(['auth'])->group(function () {
    Route::get('/booking/history', [BookingHistoryController::class, 'index'])->name('booking.history');
    
    // Pending bookings routes
    Route::get('/booking/pending', [BookingController::class, 'getPendingBookings'])->name('booking.pending');
    Route::get('/booking/pending/{id}/resume', [BookingController::class, 'resumeBooking'])->name('booking.resume');
    Route::delete('/booking/pending/{id}', [BookingController::class, 'deletePendingBooking'])->name('booking.pending.delete');
});

// Admin routes
Route::middleware(['auth', 'is_admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/bookings', [BookingsController::class, 'index'])->name('admin.bookings.index');
    Route::get('/tables', [TableController::class, 'kelolaMeja'])->name('admin.tables.index');

    Route::get('/tables/{id}/edit', [TableController::class, 'editTable'])->name('admin.tables.edit');
    Route::put('/tables/{id}', [TableController::class, 'updateTable'])->name('admin.tables.update');
});