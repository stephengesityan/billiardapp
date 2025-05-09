@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-md mx-auto bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="bg-blue-600 px-6 py-4">
                <h2 class="text-xl font-bold text-white">{{ __('Verifikasi Email Anda') }}</h2>
            </div>

            <div class="p-6">
                @if (session('resent'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                        <p>{{ __('Link verifikasi baru telah dikirim ke alamat email Anda.') }}</p>
                    </div>
                @endif

                @if (session('verified'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                        <p>{{ __('Akun Anda telah berhasil diverifikasi. Sekarang Anda dapat login.') }}</p>
                    </div>
                @endif

                <div class="mb-6">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-envelope-open-text text-blue-500 text-3xl mr-4"></i>
                        <div>
                            <p class="text-gray-700">
                                {{ __('Sebelum melanjutkan, silakan periksa email Anda untuk link verifikasi.') }}
                            </p>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ __('Email verifikasi biasanya dikirim dalam beberapa menit.') }}
                            </p>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg mb-4">
                        <p class="text-gray-600 text-sm">
                            <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                            {{ __('Verifikasi email diperlukan untuk menggunakan fitur aplikasi Ayo Venue. Pastikan Anda memverifikasi email Anda untuk akses penuh ke platform kami.') }}
                        </p>
                    </div>
                </div>

                <div class="text-center">
                    <p class="text-gray-700 mb-4">{{ __('Tidak menerima email verifikasi?') }}</p>
                    <form method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition duration-200">
                            {{ __('Kirim Ulang Verifikasi') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection