@extends('layouts.admin')

@section('content')
    <div class="p-4">
        <h1 class="text-2xl font-bold mb-4">Kelola Meja</h1>

        <div class="overflow-x-auto bg-white shadow rounded-lg">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Nama Meja</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Merek</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-2 text-left font-semibold text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($tables as $table)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-2">{{ $table->name }}</td>
                                    <td class="px-4 py-2">{{ $table->brand }}</td>
                                    <td class="px-4 py-2">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium
                                                                                                    {{ $table->status === 'Available' ? 'bg-green-100 text-green-800' :
                        ($table->status === 'Booked' ? 'bg-yellow-100 text-yellow-800' :
                            'bg-red-100 text-red-800') }}">
                                            {{ $table->status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2">
                                        <a href="{{ route('admin.tables.edit', $table->id) }}"
                                            class="text-blue-600 hover:underline">Edit</a>
                                    </td>
                                </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-4 text-center text-gray-500">Belum ada data meja.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $tables->links() }}
        </div>
    </div>
@endsection