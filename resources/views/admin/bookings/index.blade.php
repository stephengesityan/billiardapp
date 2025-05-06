@extends('layouts.admin')

@section('content')
    <div class="p-4">
        <h1 class="text-2xl font-bold mb-4">Daftar Booking</h1>

        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">User</th>
                        <th class="px-4 py-2 text-left">Meja</th>
                        <th class="px-4 py-2 text-left">Mulai</th>
                        <th class="px-4 py-2 text-left">Selesai</th>
                        <th class="px-4 py-2 text-left">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($bookings as $booking)
                        <tr class="border-b">
                            <td class="px-4 py-2">{{ $booking->user->name }}</td>
                            <td class="px-4 py-2">{{ $booking->table->name }}</td>
                            <td class="px-4 py-2">{{ \Carbon\Carbon::parse($booking->start_time)->format('H:i d/m') }}</td>
                            <td class="px-4 py-2">{{ \Carbon\Carbon::parse($booking->end_time)->format('H:i d/m') }}</td>
                            <td class="px-4 py-2">
                                <span
                                    class="text-sm px-2 py-1 rounded 
                                                    {{ $booking->status === 'booked' ? 'bg-blue-200 text-blue-800' : ($booking->status === 'selesai' ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800') }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-2 text-center text-gray-500">Belum ada data booking.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection