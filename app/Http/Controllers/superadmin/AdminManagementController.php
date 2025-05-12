<?php

namespace App\Http\Controllers\superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Booking;
use App\Models\Venue;

class AdminManagementController extends Controller
{
    /**
     * Display a listing of the admins.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'admin');
        
        // Filter by search term
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Filter by venue
        if ($request->filled('venue_id')) {
            $query->where('venue_id', $request->venue_id);
        }
        
        $admins = $query->paginate(10)->withQueryString();
        $venues = Venue::orderBy('name')->get();
        
        return view('superadmin.admin.index', compact('admins', 'venues'));
    }

    /**
     * Show the form for creating a new admin.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $venues = Venue::orderBy('id')->get();
        return view('superadmin.admin.create', compact('venues'));
    }

    /**
     * Store a newly created admin in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'venue_id' => 'required|exists:venues,id',
            'role' => 'required|in:admin,user',
        ]);

        $admin = new User();
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->password = bcrypt($request->password);
        $admin->role = $request->role;
        $admin->venue_id = $request->venue_id;
        $admin->email_verified_at = now(); // Admin langsung terverifikasi
        $admin->save();

        return redirect()->route('superadmin.admin.index')
            ->with('success', 'Admin berhasil ditambahkan!');
    }

    public function destroy($id)
{
    // Temukan user dengan role admin
    $admin = User::where('role', 'admin')->findOrFail($id);

    // Hapus semua bookings yang terkait dengan admin ini
    Booking::where('user_id', $admin->id)->delete();

    // Hapus admin
    $admin->delete();

    // Redirect dengan pesan sukses
    return redirect()->route('superadmin.admin.index')
        ->with('success', 'Admin berhasil dihapus beserta data booking yang terkait.');
}
}