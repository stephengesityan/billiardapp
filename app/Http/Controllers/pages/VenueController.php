<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Models\Venue; // Pastikan model Venue di-import
use Illuminate\Http\Request;

class VenueController extends Controller
{
    public function venue($venueName) {
        // Mengambil venue berdasarkan nama yang diberikan
        $venue = Venue::where('name', 'like', '%' . ucfirst($venueName) . '%')->first();

        // Jika venue tidak ditemukan, tampilkan error 404
        if (!$venue) {
            abort(404);
        }

        // Ambil tabel-tabel terkait dengan venue
        $tables = $venue->tables;

        // Mengirim data venue dan tabel ke view
        return view('pages.venue', [
            'venue' => $venue,
            'tables' => $tables
        ]);
    }
}
