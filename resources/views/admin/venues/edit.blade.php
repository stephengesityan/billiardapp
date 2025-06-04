@extends('layouts.admin')

@section('content')
    <div class="p-6">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.venue.index') }}" class="text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Venue</h1>
                    <p class="text-gray-600 mt-1">Perbarui informasi venue Anda</p>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- Edit Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <form action="{{ route('admin.venue.update') }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <!-- Venue Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Venue <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name', $venue->name) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                                placeholder="Masukkan nama venue">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                Alamat <span class="text-red-500">*</span>
                            </label>
                            <textarea id="address" name="address" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('address') border-red-500 @enderror"
                                placeholder="Masukkan alamat lengkap venue">{{ old('address', $venue->address) }}</textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Nomor Telepon
                            </label>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $venue->phone) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror"
                                placeholder="Contoh: 08123456789">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Operating Hours -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="open_time" class="block text-sm font-medium text-gray-700 mb-2">
                                    Jam Buka <span class="text-red-500">*</span>
                                </label>
                                <input type="time" id="open_time" name="open_time"
                                    value="{{ old('open_time', $venue->open_time ? \Carbon\Carbon::parse($venue->open_time)->format('H:i') : '') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('open_time') border-red-500 @enderror">
                                @error('open_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="close_time" class="block text-sm font-medium text-gray-700 mb-2">
                                    Jam Tutup <span class="text-red-500">*</span>
                                </label>
                                <input type="time" id="close_time" name="close_time"
                                    value="{{ old('close_time', $venue->close_time ? \Carbon\Carbon::parse($venue->close_time)->format('H:i') : '') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('close_time') border-red-500 @enderror">
                                @error('close_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <!-- Current Image -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Foto Venue Saat Ini
                            </label>
                            <div class="w-full h-48 bg-gray-100 rounded-lg overflow-hidden">
                                @if($venue->image)
                                    <img id="current-image" src="{{ asset('storage/' . $venue->image) }}"
                                        alt="{{ $venue->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- New Image Upload -->
                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700 mb-2">
                                Upload Foto Baru (Opsional)
                            </label>
                            <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(this)"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('image') border-red-500 @enderror">
                            @error('image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="text-xs text-gray-500 mt-1">Format: JPEG, PNG, JPG, GIF. Maksimal 2MB</p>

                            <!-- Image Preview -->
                            <div id="image-preview" class="mt-4 hidden">
                                <p class="text-sm font-medium text-gray-700 mb-2">Preview Foto Baru:</p>
                                <div class="w-full h-48 bg-gray-100 rounded-lg overflow-hidden">
                                    <img id="preview-img" src="" alt="Preview" class="w-full h-full object-cover">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="mt-6">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi Venue
                    </label>
                    <textarea id="description" name="description" rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                        placeholder="Masukkan deskripsi venue (fasilitas, suasana, dll)">{{ old('description', $venue->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.venue.index') }}"
                        class="px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('image-preview');
            const previewImg = document.getElementById('preview-img');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('hidden');
                }

                reader.readAsDataURL(input.files[0]);
            } else {
                preview.classList.add('hidden');
            }
        }
    </script>
@endsection