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
        $venue->load('tables'); // Eager loading untuk optimasi

        // Parsing jam operasional dari format H:i:s menjadi integer
        $openHour = (int) date('H', strtotime($venue->open_time));
        $closeHour = (int) date('H', strtotime($venue->close_time));
        
        // Mengirim data venue dengan jam operasional ke view
        return view('pages.venue', [
            'venue' => $venue,
            'openHour' => $openHour,
            'closeHour' => $closeHour
        ]);
    }
}