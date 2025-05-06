<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;

class BookingsController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['table', 'user'])
            ->orderBy('start_time', 'desc')
            ->paginate(10);

        return view('admin.bookings.index', compact('bookings'));
    }
    
}
