@extends('layouts.main')
@section('content')
    <div class="min-h-96 mx-4 md:w-3/4 md:mx-auto">
        <div class="mb-6">
            <img src="{{ asset($venue['image']) }}" alt="{{ $venue['name'] }}" class="w-full rounded-lg mb-4 mt-8">
            <h1 class="text-xl text-gray-800 font-semibold">{{ $venue['name'] }}</h1>
            <p class="text-sm text-gray-500">{{ $venue['location'] }}</p>
        </div>
        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($venue['address']) }}" target="_blank"
            class="flex items-center bg-[url('/public/images/map.jpg')] bg-cover bg-center p-4">
            <div>
                <h1 class="font-semibold">Lokasi Venue</h1>
                <p>{{ $venue['address'] }}</p>
            </div>
            <div>
                <i class="fa-solid fa-map-pin text-red-800 text-3xl"></i>
            </div>
        </a>
        <div class="mt-6">
            <div class="flex justify-between">
                <div>
                    <h1 class="text-xl text-gray-800 font-semibold">Pilih Meja</h1>
                </div>
                <div>
                    <h1 id="realTimeClock"></h1>
                </div>
            </div>
            @foreach ($venue['tables'] as $table)
                <div x-data="{ open: false }" class="border rounded-lg shadow-md p-4 mb-4">
                    <div class="flex items-center justify-between cursor-pointer" @click="open = !open">
                        <div class="flex items-center">
                            <img src="{{ asset('images/meja.jpg') }}" class="w-24">
                            <div class="ml-4">
                                <h3 class="font-semibold">{{ $table['name'] }} ({{ $table['brand'] }})</h3>
                                <p class="text-sm">
                                    <span class="{{ $table['status'] == 'Available' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $table['status'] }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="px-3 py-2 bg-gray-200 rounded-lg">
                            <span x-show="!open">▼</span>
                            <span x-show="open">▲</span>
                        </div>
                    </div>

                    <div x-show="open" x-collapse class="mt-4 p-4 border-t bg-gray-100 rounded-lg">
                        <h4 class="font-semibold mb-2">Pilih Jam Booking:</h4>
                        <select class="w-full border p-2 rounded-lg">
                            <option>10:00</option>
                            <option>11:00</option>
                            <option>12:00</option>
                            <option>13:00</option>
                        </select>
                        <button class="mt-3 px-4 py-2 bg-green-500 text-white rounded-lg w-full">Confirm Booking</button>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
    {{-- {{ dd($venue['location']) }} --}}

    <script>
        function updateClock() {
            const now = new Date();

            // Konversi ke WIB (GMT+7)
            const options = { timeZone: 'Asia/Jakarta', hour12: false };
            const timeFormatter = new Intl.DateTimeFormat('id-ID', { ...options, hour: '2-digit', minute: '2-digit', second: '2-digit' });

            document.getElementById('realTimeClock').textContent = timeFormatter.format(now);
        }

        // Update setiap detik
        setInterval(updateClock, 1000);
        updateClock(); // Panggil sekali untuk langsung tampil
    </script>

    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

@endsection