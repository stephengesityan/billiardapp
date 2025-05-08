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
                'address' => 'Jl. Hasanudin No.II, Dusun Krajan, Genteng Wetan, Kec. Genteng, Kabupaten Banyuwangi',
                'phone' => '08123456789',
                'description' => 'Tempat billiard terbaik di Genteng',
                'open_time' => '09:00',
                'close_time' => '23:00',
                'image' => 'images/billiard2.jpg',
                'tables' => [
                    ['name' => 'Table 1', 'brand' => 'Cosmic', 'status' => 'Available', 'price_per_hour' => 50000],
                    ['name' => 'Table 2', 'brand' => 'Cosmic', 'status' => 'Booked', 'price_per_hour' => 50000],
                    ['name' => 'Table 3', 'brand' => 'Cosmic', 'status' => 'Available', 'price_per_hour' => 50000],
                    ['name' => 'Table 4', 'brand' => 'Cosmic', 'status' => 'Available', 'price_per_hour' => 50000],
                    ['name' => 'Table 5', 'brand' => 'A Plus Premier', 'status' => 'Booked', 'price_per_hour' => 60000],
                    ['name' => 'Table 6', 'brand' => 'A Plus Premier', 'status' => 'Booked', 'price_per_hour' => 60000],
                ],
            ],
            'osing' => [
                'name' => 'Osing Billiard Center',
                'address' => 'Dusun Krajan, Kalirejo, Kec. Kabat, Kabupaten Banyuwangi',
                'phone' => '08123456790',
                'description' => 'Tempat billiard terbaik di Kabat',
                'open_time' => '09:00',
                'close_time' => '23:00',
                'image' => 'images/billiard3.jpg',
                'tables' => [
                    ['name' => 'Table 1', 'brand' => 'Xingjue', 'status' => 'Booked', 'price_per_hour' => 45000],
                    ['name' => 'Table 2', 'brand' => 'Xingjue', 'status' => 'Booked', 'price_per_hour' => 45000],
                    ['name' => 'Table 3', 'brand' => 'Xingjue', 'status' => 'Available', 'price_per_hour' => 45000],
                    ['name' => 'Table 4', 'brand' => 'Xingjue', 'status' => 'Available', 'price_per_hour' => 45000],
                    ['name' => 'Table 5', 'brand' => 'Xingjue', 'status' => 'Booked', 'price_per_hour' => 45000],
                    ['name' => 'Table 6', 'brand' => 'Xingjue', 'status' => 'Available', 'price_per_hour' => 45000],
                    ['name' => 'Table 7', 'brand' => 'Xingjue', 'status' => 'Available', 'price_per_hour' => 45000],
                ],
            ],
            'das' => [
                'name' => 'DAS Game & Billiard',
                'address' => 'Jl. Samiran, Jalen Parungan, Setail, Kec. Genteng, Kabupaten Banyuwangi',
                'phone' => '08123456791',
                'description' => 'Tempat billiard terbaik di Genteng',
                'open_time' => '09:00',
                'close_time' => '23:00',
                'image' => 'images/billiard4.jpg',
                'tables' => [
                    ['name' => 'Table 1', 'brand' => 'Cosmic', 'status' => 'Available', 'price_per_hour' => 47500],
                    ['name' => 'Table 2', 'brand' => 'Cosmic', 'status' => 'Booked', 'price_per_hour' => 47500],
                    ['name' => 'Table 3', 'brand' => 'Cosmic', 'status' => 'Available', 'price_per_hour' => 47500],
                    ['name' => 'Table 4', 'brand' => 'Cosmic', 'status' => 'Booked', 'price_per_hour' => 47500],
                    ['name' => 'Table 5', 'brand' => 'Cosmic', 'status' => 'Booked', 'price_per_hour' => 47500],
                    ['name' => 'Table 6', 'brand' => 'Cosmic', 'status' => 'Available', 'price_per_hour' => 47500],
                    ['name' => 'Table 7', 'brand' => 'Cosmic', 'status' => 'Available', 'price_per_hour' => 47500],
                    ['name' => 'Table 8', 'brand' => 'Cosmic', 'status' => 'Booked', 'price_per_hour' => 47500],
                ],
            ],
        ];

        foreach ($venues as $venueData) {
            // Membuat venue baru
            $venue = Venue::create([
                'name' => $venueData['name'],
                'address' => $venueData['address'],
                'phone' => $venueData['phone'],
                'description' => $venueData['description'],
                'open_time' => $venueData['open_time'],
                'close_time' => $venueData['close_time'],
                'image' => $venueData['image'],
            ]);

            // Menambahkan tabel untuk setiap venue
            foreach ($venueData['tables'] as $tableData) {
                $venue->tables()->create($tableData);
            }
        }
    }
}
