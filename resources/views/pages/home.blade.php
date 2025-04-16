@extends('layouts.main')

@section('content')
    <div class="contents h-screen">
        <div class="bg-primary py-5 md:py-10 mb-10 md:mb-16">
            <div>
                <h1 class="text-xl md:text-4xl font-bold px-12 text-center text-white uppercase">BOOKING LAPANGAN ONLINE
                    TERBAIK</h1>
            </div>
            <div class="text-center mt-5">
                <a href="https://ayo.co.id/ayo-venue-management"
                    class="text-white bg-orange-400 py-2 px-4 rounded-lg font-semibold text-sm md:text-lg">Daftarkan
                    Venue</a>
            </div>
        </div>
        <div class="flex flex-col md:flex-row lg:justify-center">
            <div class="mx-4 lg:mx-0 mb-5 flex items-center border border-gray-300 md:border-0 rounded-lg">
                <span class="text-gray-500 px-3">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text"
                    class="border w-full border-gray-300 py-3 px-4 rounded-e-lg focus:outline-none focus:ring focus:ring-blue-200 text-sm text-gray-500"
                    placeholder="Cari nama venue">
            </div>
            <div class="mx-4 lg:mx-0 mb-5 flex items-center border border-gray-300 md:border-0 rounded-lg">
                <span class="text-gray-500 px-3">
                    <i class="fas fa-location"></i>
                </span>
                <input type="text"
                    class="border w-full border-gray-300 py-3 px-4 rounded-e-lg focus:outline-none focus:ring focus:ring-blue-200 text-sm text-gray-500"
                    placeholder="Pilih Kota">
            </div>
            <div class="mx-4 lg:mx-0 mb-5 flex items-center border border-gray-300 md:border-0 rounded-lg">
                <span class="text-gray-500 px-3">
                    <i class="fa-solid fa-key"></i>
                </span>
                <input type="text"
                    class="border w-full border-gray-300 py-3 px-4 rounded-e-lg focus:outline-none focus:ring focus:ring-blue-200 text-sm text-gray-500"
                    placeholder="Billiard">
            </div>
            <div class="px-4 lg:ps-4 md:px-0 mb-5">
                <button class="w-full py-3 md:px-4 rounded-lg bg-[#F2E3E5]">
                    <i class="fa-solid fa-filter"></i>
                </button>
            </div>
            <div class="px-4">
                <button
                    class="w-full py-3 md:px-6 rounded-lg text-sm bg-primary text-white font-semibold md:whitespace-nowrap">
                    <h6>Cari venue</h6>
                </button>
            </div>
        </div>
        <hr class="my-4 md:mx-4 lg:mx-44 bg-gray-500 hidden md:block">
        <div class="md:flex md:justify-between md:mb-14">
            <div class="px-4 lg:px-44 mt-6 md:mt-0 md:flex md:flex-row">
                <h6 class="text-gray-400 text-sm">Menampilkan: </h6>
                <p class="text-gray-400 text-sm">6</p>
                <p class="text-gray-400 text-sm">venue tersedia</p>
                <hr class="my-4 md:my-0 bg-gray-500">
            </div>
            <div class="px-4 lg:px-44">
                <p class="text-sm text-gray-400 mb-10 md:mb-0">Urutkan berdasarkan: <span class="text-gray-700">Harga
                        terendah</span></p>
            </div>
        </div>
        <div class="px-4 lg:px-44 flex flex-col md:flex-row space-y-6 md:space-y-0 md:space-x-4">
            <a href="/venue/capitano" class="md:min-h-48 md:flex md:flex-col">
                <div>
                    <img src="{{ asset('images/billiard2.jpg') }}" alt="Ayo Logo" class="w-full rounded-t-lg">
                </div>
                <div class="md:flex-grow px-4 py-2 border border-gray-400 rounded-b-lg">
                    <h3 class="text-sm text-gray-400 font-semibold mb-2">Venue</h3>
                    <h1 class="text-xl text-gray-800 font-semibold">Capitano Billiard</h1>
                    <p class="text-sm text-gray-500">Genteng</p>
                    <p class="mt-10 text-gray-500 text-sm">Mulai: <span class="font-bold text-gray-800">Rp30,000
                        </span><span class="text-gray-400 font-thin text-sm">/ jam</span></p>
                </div>
            </a>
            <a href="/venue/osing" class="md:min-h-48 md:flex md:flex-col">
                <div>
                    <img src="{{ asset('images/billiard3.jpg') }}" alt="Ayo Logo" class="w-full rounded-t-lg">
                </div>
                <div class="md:flex-grow px-4 py-2 border border-gray-400 rounded-b-lg">
                    <h3 class="text-sm text-gray-400 font-semibold mb-2">Venue</h3>
                    <h1 class="text-xl text-gray-800 font-semibold">Osing Billiard Center</h1>
                    <p class="text-sm text-gray-500">Lidah</p>
                    <p class="mt-10 text-gray-500 text-sm">Mulai: <span class="font-bold text-gray-800">Rp25,000
                        </span><span class="text-gray-400 font-thin text-sm">/ jam</span></p>
                </div>
            </a>
            <a href="/venue/das" class="md:min-h-48 md:flex md:flex-col">
                <div>
                    <img src="{{ asset('images/billiard4.jpg') }}" alt="Ayo Logo" class="w-full rounded-t-lg">
                </div>
                <div class="md:flex-grow px-4 py-2 border border-gray-400 rounded-b-lg">
                    <h3 class="text-sm text-gray-400 font-semibold mb-2">Venue</h3>
                    <h1 class="text-xl text-gray-800 font-semibold">DAS Game & Billiard</h1>
                    <p class="text-sm text-gray-500">Jalen</p>
                    <p class="mt-10 text-gray-500 text-sm">Mulai: <span class="font-bold text-gray-800">Rp20,000
                        </span><span class="text-gray-400 font-thin text-sm">/ jam</span></p>
                </div>
            </a>
        </div>
    </div>
@endsection