<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Venue;
use App\Models\Table;
use App\Models\Booking;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        $venue = Venue::find(auth()->user()->venue_id);

        $todayBookings = Booking::whereDate('created_at', now())
            ->whereHas('table', function ($query) use ($venue) {
                $query->where('venue_id', $venue->id);
            })
            ->count();

        $totalTables = Table::where('venue_id', $venue->id)->count();
        $usedTables = Table::where('venue_id', $venue->id)->where('status', 'booked')->count();
        $availableTables = Table::where('venue_id', $venue->id)->where('status', 'available')->count();

        $recentBookings = Booking::whereHas('table', function ($query) use ($venue) {
            $query->where('venue_id', $venue->id);
        })
            ->latest()
            ->take(5)
            ->with(['user', 'table'])
            ->get();

        return view('admin.dashboard', compact(
            'venue',
            'todayBookings',
            'totalTables',
            'usedTables',
            'availableTables',
            'recentBookings'
        ));
    }
}
