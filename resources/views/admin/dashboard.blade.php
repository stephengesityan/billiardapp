@extends('layouts.admin')

@section('content')
    <div class="p-4">
        <h1 class="text-2xl font-bold mb-4">Admin {{ $venue->name }}</h1>
        <p>Selamat datang, {{ auth()->user()->name }}!</p>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 my-6">
            <a href="#" class="bg-blue-500 text-white p-4 rounded-lg">
                <p class="text-sm">Jumlah Booking Hari Ini</p>
                <p class="text-2xl font-bold">{{ $todayBookings }}</p>
            </a>
            <a href="#" class="bg-gray-600 text-white p-4 rounded-lg">
                <p class="text-sm">Total Meja</p>
                <p class="text-2xl font-bold">{{ $totalTables }}</p>
            </a>
            <a href="#" class="bg-red-600 text-white p-4 rounded-lg">
                <p class="text-sm">Meja Sedang Digunakan</p>
                <p class="text-2xl font-bold">{{ $usedTables }}</p>
            </a>
            <a href="#" class="bg-green-600 text-white p-4 rounded-lg">
                <p class="text-sm">Meja Tersedia</p>
                <p class="text-2xl font-bold">{{ $availableTables }}</p>
            </a>
        </div>

        <h2 class="font-semibold text-lg mt-8 mb-2">Booking Terbaru</h2>

        @if($recentBookings->isEmpty())
            <p class="text-gray-500">Belum ada booking terbaru.</p>
        @else
            <div class="bg-white rounded shadow overflow-x-auto">
                <table class="w-full table-auto">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left">Nama User</th>
                            <th class="px-4 py-2 text-left">Meja</th>
                            <th class="px-4 py-2 text-left">Waktu</th>
                            <th class="px-4 py-2 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentBookings as $booking)
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ $booking->user->name }}</td>
                                <td class="px-4 py-2">{{ $booking->table->name }}</td>
                                <td class="px-4 py-2">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                                    {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</td>
                                <td class="px-4 py-2 capitalize">{{ $booking->status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection