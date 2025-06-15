@extends('layouts.super-admin')

@section('content')
    <div class="min-h-screen bg-gray-50 flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
        <div class="w-full max-w-2xl">
            <div class="bg-white rounded-xl shadow-2xl overflow-hidden"
                style="backdrop-filter: blur(20px); background-color: rgba(255, 255, 255, 0.8);">
                <div class="p-6 sm:p-10">
                    <h2 class="text-center text-4xl font-semibold text-gray-900 mb-8">
                        {{ __('Detail Venue') }}
                    </h2>

                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                            <ul class="space-y-1 text-sm text-red-700">
                                @foreach ($errors->all() as $error)
                                    <li class="flex items-center">
                                        <svg class="h-4 w-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-11.293a1 1 0 00-1.414-1.414L10 8.586 7.707 6.293a1 1 0 00-1.414 1.414L8.586 10l-2.293 2.293a1 1 0 101.414 1.414L10 11.414l2.293 2.293a1 1 0 001.414-1.414L11.414 10l2.293-2.293z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $error }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('superadmin.venue.update', $venue->id) }}"
                        enctype="multipart/form-data" x-data="venueForm()" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid md:grid-cols-2 gap-6">
                            {{-- Nama Venue --}}
                            <div class="col-span-2 md:col-span-1">
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Nama Venue') }}
                                </label>
                                <input type="text" id="name" name="name" value="{{ old('name', $venue->name) }}" required
                                    autocomplete="name" autofocus
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300 ease-in-out"
                                    placeholder="Masukkan nama venue" disabled>
                            </div>

                            {{-- Nomor Telepon --}}
                            <div class="col-span-2 md:col-span-1">
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Nomor Telepon') }}
                                </label>
                                <input type="tel" id="phone" name="phone" value="{{ old('phone', $venue->phone) }}" required
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300 ease-in-out"
                                    placeholder="Masukkan nomor telepon" disabled>
                            </div>
                        </div>

                        {{-- Alamat --}}
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Alamat') }}
                            </label>
                            <textarea id="address" name="address" required rows="3"
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300 ease-in-out"
                                placeholder="Masukkan alamat lengkap venue"
                                disabled>{{ old('address', $venue->address) }}</textarea>
                        </div>

                        {{-- Deskripsi --}}
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Deskripsi') }}
                            </label>
                            <textarea id="description" name="description" rows="4" required
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300 ease-in-out"
                                placeholder="Berikan deskripsi venue"
                                disabled>{{ old('description', $venue->description) }}</textarea>
                        </div>

                        {{-- Jam Operasional --}}
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label for="open_time" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Jam Buka') }}
                                </label>
                                <input type="time" id="open_time" name="open_time"
                                    value="{{ old('open_time', date('H:i', strtotime($venue->open_time))) }}" required
                                    disabled
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300 ease-in-out">
                            </div>
                            <div>
                                <label for="close_time" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Jam Tutup') }}
                                </label>
                                <input type="time" id="close_time" name="close_time"
                                    value="{{ old('close_time', date('H:i', strtotime($venue->close_time))) }}" required
                                    disabled
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300 ease-in-out">
                            </div>
                        </div>

                        {{-- Status --}}
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Status') }}
                            </label>
                            <select id="status" name="status" required disabled
                                class="block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300 ease-in-out">
                                <option value="open" {{ old('status', $venue->status) == 'open' ? 'selected' : '' }}>
                                    {{ __('Open') }}
                                </option>
                                <option value="close" {{ old('status', $venue->status) == 'close' ? 'selected' : '' }}>
                                    {{ __('Close') }}
                                </option>
                            </select>
                        </div>

                        {{-- Upload Gambar --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Gambar Venue') }}
                            </label>

                            {{-- Preview gambar existing --}}
                            @if($venue->image)
                                <div class="mb-4">
                                    <div class="text-sm text-gray-600 mb-2">{{ __('Gambar saat ini:') }}</div>
                                    <img src="{{ Storage::url($venue->image) }}" alt="{{ $venue->name }}"
                                        class="h-32 w-auto object-cover rounded-lg border border-gray-200 shadow-sm">
                                </div>
                            @endif

                            {{-- <div x-ref="dropzone" @dragover.prevent="dragover = true"
                                @dragleave.prevent="dragover = false" @drop.prevent="handleDrop($event)"
                                class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg transition duration-300 ease-in-out"
                                :class="dragover ? 'border-blue-500 bg-blue-50' : 'hover:border-blue-500'">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                        viewBox="0 0 48 48" aria-hidden="true">
                                        <path
                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600 justify-center">
                                        <label for="image"
                                            class="relative cursor-pointer rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-blue-500">
                                            <span>{{ __('Unggah file baru') }}</span>
                                            <input id="image" name="image" type="file" class="sr-only" accept="image/*"
                                                @change="handleFileSelect($event)">
                                        </label>
                                        <p class="pl-1">{{ __('atau seret dan lepas') }}</p>
                                    </div>
                                    <p class="text-xs text-gray-500">
                                        {{ __('PNG, JPG, GIF hingga 2MB') }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ __('Biarkan kosong jika tidak ingin mengubah gambar') }}
                                    </p>
                                    <p x-text="fileName" class="text-sm text-gray-600 mt-2"></p>
                                </div>
                            </div> --}}
                        </div>

                        {{-- Tombol Aksi --}}
                        <div class="flex justify-end space-x-4 pt-6">
                            <a href="{{ route('superadmin.venue.index') }}"
                                class="px-6 py-3 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition duration-300 ease-in-out">
                                {{ __('Kembali') }}
                            </a>
                            {{-- <button type="submit"
                                class="px-6 py-3 bg-blue-500 text-white rounded-lg font-medium hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-300 ease-in-out">
                                {{ __('Perbarui') }}
                            </button> --}}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
        <script>
            function venueForm() {
                return {
                    dragover: false,
                    fileName: '',
                    handleFileSelect(event) {
                        const file = event.target.files[0];
                        this.fileName = file ? file.name : '';
                    },
                    handleDrop(event) {
                        this.dragover = false;
                        const file = event.dataTransfer.files[0];
                        if (file) {
                            document.getElementById('image').files = event.dataTransfer.files;
                            this.fileName = file.name;
                        }
                    }
                }
            }
        </script>
    @endpush
@endsection