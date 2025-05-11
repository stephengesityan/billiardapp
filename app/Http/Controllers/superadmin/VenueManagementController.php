<?php

namespace App\Http\Controllers\superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Venue;
use Illuminate\Support\Facades\Storage;

class VenueManagementController extends Controller
{
    /**
     * Display a listing of the venues.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $venues = Venue::paginate(10);
        return view('superadmin.venue.index', compact('venues'));
    }

    /**
     * Show the form for creating a new venue.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('superadmin.venue.create');
    }

    /**
     * Store a newly created venue in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'description' => 'required|string',
            'phone' => 'required|string|max:20',
            'open_time' => 'required|date_format:H:i',
            'close_time' => 'required|date_format:H:i',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            // 'status' => 'required|in:active,inactive',
        ]);

        // Handle file upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            // Get the file extension
            $extension = $request->file('image')->getClientOriginalExtension();
            
            // Generate a unique filename
            $imageName = time() . '_' . uniqid() . '.' . $extension;
            
            // Store the file in the public disk under venues directory
            $imagePath = $request->file('image')->storeAs('venues', $imageName, 'public');
        }

        $venue = new Venue();
        $venue->name = $request->name;
        $venue->address = $request->address;
        $venue->description = $request->description;
        $venue->phone = $request->phone;
        $venue->open_time = $request->open_time;
        $venue->close_time = $request->close_time;
        $venue->image = $imagePath;
        // $venue->status = $request->status;
        $venue->save();

        return redirect()->route('superadmin.venue.index')
            ->with('success', 'Venue berhasil ditambahkan!');
    }

    /**
     * Show the form for editing the specified venue.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $venue = Venue::findOrFail($id);
        return view('superadmin.venue.edit', compact('venue'));
    }

    /**
     * Update the specified venue in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'description' => 'required|string',
            'phone' => 'required|string|max:20',
            'open_time' => 'required|date_format:H:i',
            'close_time' => 'required|date_format:H:i',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // 'status' => 'required|in:active,inactive',
        ]);

        $venue = Venue::findOrFail($id);
        
        // Handle file upload if a new image is provided
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($venue->image && Storage::disk('public')->exists($venue->image)) {
                Storage::disk('public')->delete($venue->image);
            }
            
            // Get the file extension
            $extension = $request->file('image')->getClientOriginalExtension();
            
            // Generate a unique filename
            $imageName = time() . '_' . uniqid() . '.' . $extension;
            
            // Store the file in the public disk under venues directory
            $imagePath = $request->file('image')->storeAs('venues', $imageName, 'public');
            
            $venue->image = $imagePath;
        }

        $venue->name = $request->name;
        $venue->address = $request->address;
        $venue->description = $request->description;
        $venue->phone = $request->phone;
        $venue->open_time = $request->open_time;
        $venue->close_time = $request->close_time;
        // $venue->status = $request->status;
        $venue->save();

        return redirect()->route('superadmin.venue.index')
            ->with('success', 'Venue berhasil diperbarui!');
    }

    /**
     * Remove the specified venue from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $venue = Venue::findOrFail($id);
        
        // Delete the venue image if exists
        if ($venue->image && Storage::disk('public')->exists($venue->image)) {
            Storage::disk('public')->delete($venue->image);
        }
        
        $venue->delete();

        return redirect()->route('superadmin.venue.index')
            ->with('success', 'Venue berhasil dihapus!');
    }
}