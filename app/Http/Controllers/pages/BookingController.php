<?php

namespace App\Http\Controllers\pages;
use App\Http\Controllers\Controller;

use App\Models\Booking;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function store(Request $request) {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        // Cek apakah meja sedang dibooking pada waktu tersebut
        $conflict = Booking::where('table_id', $request->table_id)
                ->where(function($query) use ($request) {
                    $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                    ->orWhere(function($query) use ($request) {
                    $query->where('start_time', '<', $request->start_time)
                    ->where('end_time', '>', $request->end_time);
                });
            })
            ->where('status', '!=', 'cancelled') // skip booking yang dibatalkan
            ->exists();

        if ($conflict) {
            return response()->json(['message' => 'Meja sudah dibooking di jam tersebut'], 409);
        }

        Booking::create([
            'table_id' => $request->table_id,
            'user_id' => Auth::id(),
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => 'booked',
        ]);

        return response()->json(['message' => 'Booking berhasil']);
    }
}
