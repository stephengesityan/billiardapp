<?php

namespace App\Http\Controllers\pages;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingHistoryController extends Controller
{
    public function index()
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->with(['table.venue'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('pages.booking-history', compact('bookings'));
    }
}