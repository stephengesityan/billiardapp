@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="bg-green-600 px-6 py-4">
                <h2 class="text-xl font-bold text-white">{{ __('Email Berhasil Diverifikasi!') }}</h2>
            </div>

            <div class="p-6">
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 mb-4">
                        <i class="fas fa-check text-green-500 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800">{{ __('Verifikasi Berhasil') }}</h3>
                    <p class="text-gray-600 mt-2">
                        {{ __('Selamat! Email Anda telah berhasil diverifikasi.') }}
                    </p>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <p class="text-gray-600 text-sm">
                        <i class="fas fa-info-circle text-green-500 mr-2"></i>
                        {{ __('Anda sekarang memiliki akses penuh ke semua fitur Ayo Venue.') }}
                    </p>
                </div>

                <div class="text-center">
                    <a href="{{ route('index') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition duration-200 inline-block">
                        {{ __('Login Sekarang') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection