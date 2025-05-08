@extends('layouts.main')
@section('content')
    <div class="min-h-96 mx-4 md:w-3/4 md:mx-auto py-8">
        <h1 class="text-2xl font-bold mb-6">Riwayat Booking</h1>

        @if($bookings->isEmpty())
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <p class="text-gray-500">Anda belum memiliki riwayat booking.</p>
                <a href="{{ route('venues.index') }}" class="mt-4 inline-block bg-blue-500 text-white px-4 py-2 rounded-lg">Cari
                    Venue</a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($bookings as $booking)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="p-4 border-b {{ $booking->start_time > now() ? 'bg-green-50' : 'bg-gray-50' }}">
                            <div class="flex justify-between items-center">
                                <h3 class="font-semibold text-lg">{{ $booking->table->venue->name }}</h3>
                                <span
                                    class="px-3 py-1 rounded-full text-sm {{ $booking->start_time > now() ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-800' }}">
                                    {{ $booking->start_time > now() ? 'Upcoming' : 'Completed' }}
                                </span>
                            </div>
                        </div>
                        <div class="p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm text-gray-500">Meja</p>
                                    <p class="font-medium">{{ $booking->table->name }} ({{ $booking->table->brand }})</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Tanggal & Waktu</p>
                                    <p class="font-medium">{{ \Carbon\Carbon::parse($booking->start_time)->format('d M Y') }},
                                        {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Durasi</p>
                                    <p class="font-medium">
                                        {{ \Carbon\Carbon::parse($booking->start_time)->diffInHours($booking->end_time) }} Jam
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Total Bayar</p>
                                    <p class="font-medium">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Status Pembayaran</p>
                                    <p class="font-medium capitalize">{{ $booking->status }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Metode Pembayaran</p>
                                    <p class="font-medium capitalize">{{ $booking->payment_method ?? '-' }}</p>
                                </div>
                            </div>

                            @if($booking->start_time > now() && $booking->status == 'paid')
                                <div class="mt-4 flex justify-end">
                                    <a href="{{ route('venue', $booking->table->venue->name) }}"
                                        class="text-blue-500 hover:underline">Lihat Venue</a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
@endsection