<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Venue;
use App\Models\Table;

class VenueSeeder extends Seeder
{
    public function run(): void
    {
        $venues = [
            'capitano' => [
                'name' => 'Capitano Billiard',
                'location' => 'Genteng',
                'address' => 'Jl. Hasanudin No.II, Dusun Krajan, Genteng Wetan, Kec. Genteng, Kabupaten Banyuwangi',
                'price' => 30000,
                'image' => 'images/billiard2.jpg',
                'tables' => [
                    ['name' => 'Table 1', 'brand' => 'Cosmic', 'status' => 'Available'],
                    ['name' => 'Table 2', 'brand' => 'Cosmic', 'status' => 'Booked'],
                    ['name' => 'Table 3', 'brand' => 'Cosmic', 'status' => 'Available'],
                    ['name' => 'Table 4', 'brand' => 'Cosmic', 'status' => 'Available'],
                    ['name' => 'Table 5', 'brand' => 'A Plus Premier', 'status' => 'Booked'],
                    ['name' => 'Table 6', 'brand' => 'A Plus Premier', 'status' => 'Booked'],
                ],
            ],
            'osing' => [
                'name' => 'Osing Billiard Center',
                'location' => 'Lidah',
                'address' => 'Dusun Krajan, Kalirejo, Kec. Kabat, Kabupaten Banyuwangi',
                'price' => 25000,
                'image' => 'images/billiard3.jpg',
                'tables' => [
                    ['name' => 'Table 1', 'brand' => 'Xingjue', 'status' => 'Booked'],
                    ['name' => 'Table 2', 'brand' => 'Xingjue', 'status' => 'Booked'],
                    ['name' => 'Table 3', 'brand' => 'Xingjue', 'status' => 'Available'],
                    ['name' => 'Table 4', 'brand' => 'Xingjue', 'status' => 'Available'],
                    ['name' => 'Table 5', 'brand' => 'Xingjue', 'status' => 'Booked'],
                    ['name' => 'Table 6', 'brand' => 'Xingjue', 'status' => 'Available'],
                    ['name' => 'Table 7', 'brand' => 'Xingjue', 'status' => 'Available'],
                ],
            ],
            'das' => [
                'name' => 'DAS Game & Billiard',
                'location' => 'Jalen',
                'address' => 'Jl. Samiran, Jalen Parungan, Setail, Kec. Genteng, Kabupaten Banyuwangi',
                'price' => 20000,
                'image' => 'images/billiard4.jpg',
                'tables' => [
                    ['name' => 'Table 1', 'brand' => 'Cosmic', 'status' => 'Available'],
                    ['name' => 'Table 2', 'brand' => 'Cosmic', 'status' => 'Booked'],
                    ['name' => 'Table 3', 'brand' => 'Cosmic', 'status' => 'Available'],
                    ['name' => 'Table 4', 'brand' => 'Cosmic', 'status' => 'Booked'],
                    ['name' => 'Table 5', 'brand' => 'Cosmic', 'status' => 'Booked'],
                    ['name' => 'Table 6', 'brand' => 'Cosmic', 'status' => 'Available'],
                    ['name' => 'Table 7', 'brand' => 'Cosmic', 'status' => 'Available'],
                    ['name' => 'Table 8', 'brand' => 'Cosmic', 'status' => 'Booked'],
                ],
            ],
        ];

        foreach ($venues as $venueData) {
            // Membuat venue baru
            $venue = Venue::create([
                'name' => $venueData['name'],
                'location' => $venueData['location'],
                'address' => $venueData['address'],
                'price' => $venueData['price'],
                'image' => $venueData['image'],
            ]);

            // Menambahkan tabel untuk setiap venue
            foreach ($venueData['tables'] as $tableData) {
                $venue->tables()->create($tableData); // Menambahkan meja ke venue
            }
        }
    }
}
