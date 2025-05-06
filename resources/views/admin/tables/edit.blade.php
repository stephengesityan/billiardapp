@extends('layouts.admin')

@section('content')
    <div class="p-4">
        <h1 class="text-2xl font-bold mb-4">Edit Meja: {{ $table->name }}</h1>

        <form action="{{ route('admin.tables.update', $table->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700">Nama Meja</label>
                <input type="text" name="name" value="{{ $table->name }}" class="w-full border border-gray-300 p-2 rounded">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Merek</label>
                <input type="text" name="brand" value="{{ $table->brand }}"
                    class="w-full border border-gray-300 p-2 rounded">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" class="w-full border border-gray-300 p-2 rounded">
                    <option value="Available" {{ $table->status === 'Available' ? 'selected' : '' }}>Available</option>
                    <option value="Booked" {{ $table->status === 'Booked' ? 'selected' : '' }}>Booked</option>
                    <option value="Unavailable" {{ $table->status === 'Unavailable' ? 'selected' : '' }}>Unavailable</option>
                </select>
            </div>

            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
        </form>
    </div>
@endsection