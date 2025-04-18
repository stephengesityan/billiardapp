<?php

use App\Http\Controllers\pages\HomeController;
use App\Http\Controllers\pages\VenueController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', [HomeController::class, "index"])->name('index');
Route::get('/venue/{venueName}', [VenueController::class, "venue"])->name('venue');

Auth::routes();
