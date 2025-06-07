<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BookingsExport;

class BookingsController extends Controller
{
    public function index(Request $request)
    {
        // Ambil venue_id dari admin yang sedang login
        // Sesuaikan dengan struktur database kamu:
        $adminVenueId = auth()->user()->venue_id; // Asumsi admin punya kolom venue_id
        
        // Query booking dengan filter venue terlebih dahulu
        $query = Booking::with(['table', 'user'])
            ->whereHas('table', function ($q) use ($adminVenueId) {
                $q->where('venue_id', $adminVenueId);
            });

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('table', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%");
                });
            });
        }

        // Status filter
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->has('date_from') && !empty($request->date_from)) {
            $dateFrom = Carbon::parse($request->date_from)->startOfDay();
            $query->where('start_time', '>=', $dateFrom);
        }

        if ($request->has('date_to') && !empty($request->date_to)) {
            $dateTo = Carbon::parse($request->date_to)->endOfDay();
            $query->where('start_time', '<=', $dateTo);
        }

        // Sorting
        $sortColumn = $request->sort ?? 'start_time';
        $sortDirection = $request->direction ?? 'desc';

        // Handle related column sorting
        if ($sortColumn === 'user') {
            $query->join('users', 'bookings.user_id', '=', 'users.id')
                  ->select('bookings.*')
                  ->orderBy('users.name', $sortDirection);
        } elseif ($sortColumn === 'table') {
            $query->join('tables', 'bookings.table_id', '=', 'tables.id')
                  ->select('bookings.*')
                  ->orderBy('tables.name', $sortDirection);
        } else {
            $query->orderBy($sortColumn, $sortDirection);
        }

        $bookings = $query->paginate(20)->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    public function show($id)
    {
        // Pastikan booking yang dilihat adalah milik venue admin
        $adminVenueId = auth()->user()->venue_id;
        
        $booking = Booking::with(['table', 'user'])
            ->whereHas('table', function ($q) use ($adminVenueId) {
                $q->where('venue_id', $adminVenueId);
            })
            ->findOrFail($id);
            
        return view('admin.bookings.show', compact('booking'));
    }

    public function edit($id)
    {
        $adminVenueId = auth()->user()->venue_id;
        
        // Pastikan booking yang diedit adalah milik venue admin
        $booking = Booking::whereHas('table', function ($q) use ($adminVenueId) {
            $q->where('venue_id', $adminVenueId);
        })->findOrFail($id);
        
        // Hanya tampilkan tables dari venue admin
        $tables = Table::where('venue_id', $adminVenueId)->get();
        
        return view('admin.bookings.edit', compact('booking', 'tables'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $adminVenueId = auth()->user()->venue_id;
        
        // Pastikan booking yang diupdate adalah milik venue admin
        $booking = Booking::whereHas('table', function ($q) use ($adminVenueId) {
            $q->where('venue_id', $adminVenueId);
        })->findOrFail($id);
        
        // Validasi tambahan: pastikan table_id yang dipilih juga milik venue admin
        $table = Table::where('id', $request->table_id)
            ->where('venue_id', $adminVenueId)
            ->firstOrFail();
        
        $booking->update($request->all());

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking berhasil diperbarui');
    }

    public function complete($id)
    {
        $adminVenueId = auth()->user()->venue_id;
        
        $booking = Booking::whereHas('table', function ($q) use ($adminVenueId) {
            $q->where('venue_id', $adminVenueId);
        })->findOrFail($id);
        
        $booking->status = 'selesai';
        $booking->save();

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking berhasil diselesaikan');
    }

    public function cancel($id)
    {
        $adminVenueId = auth()->user()->venue_id;
        
        $booking = Booking::whereHas('table', function ($q) use ($adminVenueId) {
            $q->where('venue_id', $adminVenueId);
        })->findOrFail($id);
        
        $booking->status = 'cancelled';
        $booking->save();

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking berhasil dibatalkan');
    }

    public function export(Request $request)
    {
        $adminVenueId = auth()->user()->venue_id;
        $filename = 'bookings-' . Carbon::now()->format('Y-m-d') . '.xlsx';
        
        // Pass venue_id ke export class jika diperlukan
        return Excel::download(new BookingsExport($request, $adminVenueId), $filename);
    }
}