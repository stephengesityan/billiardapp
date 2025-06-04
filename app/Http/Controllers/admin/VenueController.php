<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class VenueController extends Controller
{
    /**
     * Display venue management page for current admin
     */
    public function index()
    {
        // Get current admin's venue
        $venue = auth()->user()->venue;
        
        if (!$venue) {
            return redirect()->route('admin.dashboard')->with('error', 'Anda belum memiliki venue yang ditugaskan.');
        }

        // Check for auto reopen
        if ($venue->checkAutoReopen()) {
            session()->flash('success', 'Venue telah dibuka kembali secara otomatis!');
        }
        
        return view('admin.venues.index', compact('venue'));
    }
    
    /**
     * Show the form for editing venue
     */
    public function edit()
    {
        $venue = auth()->user()->venue;
        
        if (!$venue) {
            return redirect()->route('admin.dashboard')->with('error', 'Anda belum memiliki venue yang ditugaskan.');
        }
        
        return view('admin.venues.edit', compact('venue'));
    }
    
    /**
     * Update venue information
     */
    public function update(Request $request)
    {
        $venue = auth()->user()->venue;
        
        if (!$venue) {
            return redirect()->route('admin.dashboard')->with('error', 'Anda belum memiliki venue yang ditugaskan.');
        }
        
        // Validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:20',
            'description' => 'nullable|string|max:1000',
            'open_time' => 'required|date_format:H:i',
            'close_time' => 'required|date_format:H:i',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
        ], [
            'name.required' => 'Nama venue harus diisi.',
            'address.required' => 'Alamat venue harus diisi.',
            'open_time.required' => 'Jam buka harus diisi.',
            'open_time.date_format' => 'Format jam buka tidak valid (gunakan format HH:MM).',
            'close_time.required' => 'Jam tutup harus diisi.',
            'close_time.date_format' => 'Format jam tutup tidak valid (gunakan format HH:MM).',
            'image.image' => 'File yang diupload harus berupa gambar.',
            'image.mimes' => 'Gambar harus berformat: jpeg, png, jpg, atau gif.',
            'image.max' => 'Ukuran gambar maksimal 2MB.',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                           ->withErrors($validator)
                           ->withInput();
        }
        
        try {
            // Handle image upload
            $imagePath = $venue->image; // Keep current image by default
            
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($venue->image && Storage::disk('public')->exists($venue->image)) {
                    Storage::disk('public')->delete($venue->image);
                }
                
                // Store new image
                $imagePath = $request->file('image')->store('venues', 'public');
            }
            
            // Prepare update data
            $updateData = [
                'name' => $request->name,
                'address' => $request->address,
                'phone' => $request->phone,
                'description' => $request->description,
                'image' => $imagePath,
            ];

            // Only update operating hours if venue is open
            if ($venue->status === 'open') {
                $updateData['open_time'] = $request->open_time;
                $updateData['close_time'] = $request->close_time;
            } else {
                // If venue is closed, update original times
                $updateData['original_open_time'] = $request->open_time;
                $updateData['original_close_time'] = $request->close_time;
            }
            
            // Update venue data
            $venue->update($updateData);
            
            return redirect()->route('admin.venue.index')
                           ->with('success', 'Informasi venue berhasil diperbarui!');
                           
        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Terjadi kesalahan saat memperbarui venue: ' . $e->getMessage())
                           ->withInput();
        }
    }

    /**
     * Toggle venue status (open/close)
     */
    public function toggleStatus(Request $request)
    {
        $venue = auth()->user()->venue;
        
        if (!$venue) {
            return response()->json(['error' => 'Venue tidak ditemukan'], 404);
        }

        try {
            if ($venue->status === 'open') {
                // Closing venue - validate required fields
                $validator = Validator::make($request->all(), [
                    'close_reason' => 'required|string|max:500',
                    'reopen_date' => 'required|date|after:today',
                ], [
                    'close_reason.required' => 'Alasan penutupan harus diisi.',
                    'reopen_date.required' => 'Tanggal buka kembali harus diisi.',
                    'reopen_date.date' => 'Format tanggal tidak valid.',
                    'reopen_date.after' => 'Tanggal buka kembali harus setelah hari ini.',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'error' => 'Validasi gagal',
                        'errors' => $validator->errors()
                    ], 422);
                }

                $venue->closeVenue($request->close_reason, $request->reopen_date);
                $message = 'Venue berhasil ditutup!';
            } else {
                // Opening venue
                $venue->openVenue();
                $message = 'Venue berhasil dibuka!';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'status' => $venue->status,
                'venue' => [
                    'status' => $venue->status,
                    'close_reason' => $venue->close_reason,
                    'reopen_date' => $venue->reopen_date ? $venue->reopen_date->format('d M Y') : null,
                    'open_time' => $venue->open_time ? Carbon::parse($venue->open_time)->format('H:i') : null,
                    'close_time' => $venue->close_time ? Carbon::parse($venue->close_time)->format('H:i') : null,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}