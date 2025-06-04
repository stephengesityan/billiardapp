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
        $query = Booking::with(['table', 'user']);

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
        $booking = Booking::with(['table', 'user'])->findOrFail($id);
        return view('admin.bookings.show', compact('booking'));
    }

    public function edit($id)
    {
        $booking = Booking::findOrFail($id);
        $tables = Table::all();
        return view('admin.bookings.edit', compact('booking', 'tables'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $booking = Booking::findOrFail($id);
        $booking->update($request->all());

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking berhasil diperbarui');
    }

    public function complete($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->status = 'selesai';
        $booking->save();

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking berhasil diselesaikan');
    }

    public function cancel($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->status = 'cancelled';
        $booking->save();

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking berhasil dibatalkan');
    }

    public function export(Request $request)
    {
        $filename = 'bookings-' . Carbon::now()->format('Y-m-d') . '.xlsx';
        return Excel::download(new BookingsExport($request), $filename);
    }
}