<?php

use App\Http\Controllers\pages\HomeController;
use App\Http\Controllers\pages\VenueController;
use App\Http\Controllers\pages\BookingController;
use App\Http\Controllers\admin\BookingsController;
use App\Http\Controllers\admin\TableController;
use App\Http\Controllers\admin\AdminController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Auth::routes();
Route::get('/', [HomeController::class, "index"])->name('index');
Route::get('/venue/{venueName}', [VenueController::class, "venue"])->name('venue');
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
Route::get('/booking/schedules', [BookingController::class, 'getBookedSchedules'])->name('booking.schedules');
Route::post('/booking/payment', [BookingController::class, 'processPayment'])->name('booking.payment');
Route::get('/booking/payment/{bookingId}', [BookingController::class, 'checkPaymentStatus'])->name('booking.payment.status');
Route::post('/payment/notification', [BookingController::class, 'handleNotification'])->name('payment.notification');
Route::middleware(['auth', 'is_admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/bookings', [BookingsController::class, 'index'])->name('admin.bookings.index');
    Route::get('/tables', [TableController::class, 'kelolaMeja'])->name('admin.tables.index');

    Route::get('/tables/{id}/edit', [TableController::class, 'editTable'])->name('admin.tables.edit');
    Route::put('/tables/{id}', [TableController::class, 'updateTable'])->name('admin.tables.update');

});