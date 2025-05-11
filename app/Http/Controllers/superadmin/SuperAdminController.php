<?php

namespace App\Http\Controllers\superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Venue;
use App\Models\Table;

class SuperAdminController extends Controller
{
    /**
     * Display the superadmin dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Menghitung jumlah admin, venue, user, venue aktif, dan meja
        $adminCount = User::where('role', 'admin')->count();
        $venueCount = Venue::count();
        $userCount = User::where('role', 'user')->count();
        // $activeVenueCount = Venue::where('status', 'active')->count();
        $tableCount = Table::count();

        return view('superadmin.dashboard', compact(
            'adminCount',
            'venueCount',
            'userCount',
            // 'activeVenueCount',
            'tableCount'
        ));
    }
}