<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }
    
    public function index(Request $request) {
        // Start with base query
        $query = Venue::query();

        // Search by venue name
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        // Filter by city/location (assuming this is based on address)
        if ($request->filled('location')) {
            $query->where('address', 'like', '%' . $request->input('location') . '%');
        }

        // Paginate the results
        $venues = $query->paginate(6);

        // Retain the search parameters for pagination links
        $venues->appends($request->only(['name', 'location']));

        return view('pages.home', compact('venues'));
    }
}
