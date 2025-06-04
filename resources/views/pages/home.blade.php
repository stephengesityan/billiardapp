@extends('layouts.main')

@section('content')
    <div class="contents h-screen">
        <div class="bg-primary py-5 md:py-10 mb-10 md:mb-16">
            <div>
                <h1 class="text-xl md:text-4xl font-bold px-12 text-center text-white uppercase">APLIKASI BOOKING MEJA
                    TERBAIK</h1>
            </div>
            <div class="text-center mt-5">
                <a href="https://wa.me/6285730595855?text=Halo%2C%20saya%20tertarik%20untuk%20mendaftarkan%20venue%20saya"
                    target="_blank" class="text-white bg-yellow-500 py-2 px-4 rounded-lg font-semibold text-sm md:text-lg">
                    Daftarkan Venue
                </a>
            </div>
        </div>

        <form action="{{ route('home') }}" method="GET" class="flex flex-col md:flex-row lg:justify-center">
            <div class="mx-4 lg:mx-0 mb-5 flex items-center border border-gray-300 md:border-0 rounded-lg">
                <span class="text-gray-500 px-3">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" name="name"
                    class="border w-full border-gray-300 py-3 px-4 rounded-e-lg focus:outline-none focus:ring focus:ring-blue-200 text-sm text-gray-500"
                    placeholder="Cari nama venue" value="{{ request('name') }}">
            </div>
            <div class="mx-4 lg:mx-0 mb-5 flex items-center border border-gray-300 md:border-0 rounded-lg">
                <span class="text-gray-500 px-3">
                    <i class="fas fa-location"></i>
                </span>
                <input type="text" name="location"
                    class="border w-full border-gray-300 py-3 px-4 rounded-e-lg focus:outline-none focus:ring focus:ring-blue-200 text-sm text-gray-500"
                    placeholder="Pilih Kota" value="{{ request('location') }}">
            </div>
            <div class="px-4">
                <button type="submit"
                    class="w-full py-3 md:px-6 rounded-lg text-sm bg-primary text-white font-semibold md:whitespace-nowrap">
                    <h6>Cari venue</h6>
                </button>
            </div>
        </form>
        <hr class="my-4 md:mx-4 lg:mx-44 bg-gray-500 hidden md:block">
        <div class="md:flex md:justify-between md:mb-14">
            <div class="px-4 lg:px-44 mt-6 md:mt-0 md:flex md:flex-row">
                <h6 class="text-gray-400 text-sm">Menampilkan: </h6>
                <p class="text-gray-400 text-sm ml-1">{{ $venues->total() }}</p>
                <p class="text-gray-400 text-sm ml-1">venue tersedia</p>
                <hr class="my-4 md:my-0 bg-gray-500">
            </div>
        </div>
        <div class="px-4 lg:px-44">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse ($venues as $venue)
                    <a href="{{ route('venue', ['venueName' => $venue->name]) }}"
                        class="flex flex-col h-full border border-gray-400 rounded-lg overflow-hidden">
                        <img src="{{ Storage::url($venue->image) }}" alt="{{ $venue->name }}" class="w-full h-48 object-cover">

                        <div class="flex-grow px-4 py-2">
                            <h3 class="text-sm text-gray-400 font-semibold mb-2">Venue</h3>
                            <h1 class="text-xl text-gray-800 font-semibold">{{ $venue->name }}</h1>
                            <p class="text-sm text-gray-600 mt-1">
                                <i class="fa-regular fa-clock"></i>
                                Buka: {{ date('H:i', strtotime($venue['open_time'])) }} -
                                {{ date('H:i', strtotime($venue['close_time'])) }}
                            </p>
                            <p class="mt-10 text-gray-500 text-sm">Mulai:
                                <span class="font-bold text-gray-800">Rp30,000</span>
                                <span class="text-gray-400 font-thin text-sm">/ jam</span>
                            </p>
                        </div>
                    </a>
                @empty
                    <p class="text-center col-span-full text-gray-500">Belum ada venue tersedia.</p>
                @endforelse
            </div>

            {{-- Pagination Links --}}
            <div class="mt-4">
                {{ $venues->links() }}
            </div>
        </div>
    </div>
@endsection