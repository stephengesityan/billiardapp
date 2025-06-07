@extends('layouts.admin')

@section('content')
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-4 md:mb-0">
                <i class="fas fa-calendar-check mr-2"></i>Daftar Booking
            </h1>

            <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                </a>
                {{-- <a href="{{ route('admin.bookings.export') }}" class="btn btn-success">
                    <i class="fas fa-file-excel mr-1"></i> Export Excel
                </a> --}}
            </div>
        </div>

        <!-- Search and Filter Card -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <form action="{{ route('admin.bookings.index') }}" method="GET" class="space-y-4">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="w-full md:w-1/4">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                        <div class="relative">
                            <input type="text" name="search" id="search" placeholder="Cari user atau meja..."
                                class="form-input w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"
                                value="{{ request('search') }}">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    {{-- <div class="w-full md:w-1/4">
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="status"
                            class="form-select w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200">
                            <option value="">Semua Status</option>
                            <option value="booked" {{ request('status') == 'booked' ? 'selected' : '' }}>Booked</option>
                            <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan
                            </option>
                        </select>
                    </div> --}}

                    <div class="w-full md:w-1/4">
                        <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Dari Tanggal</label>
                        <input type="date" name="date_from" id="date_from"
                            class="form-input w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"
                            value="{{ request('date_from') }}">
                    </div>

                    <div class="w-full md:w-1/4">
                        <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">Sampai Tanggal</label>
                        <input type="date" name="date_to" id="date_to"
                            class="form-input w-full rounded-md border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200"
                            value="{{ request('date_to') }}">
                    </div>
                </div>

                <div class="flex justify-end gap-2">
                    <a href="{{ route('admin.bookings.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo mr-1"></i> Reset
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter mr-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Bookings Table Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h2 class="font-semibold text-lg text-gray-700">Data Booking</h2>
                <div class="text-sm text-gray-500">
                    Total: <span class="font-semibold">{{ $bookings->total() }}</span> data
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('admin.bookings.index', array_merge(request()->all(), ['sort' => 'user', 'direction' => request('direction') == 'asc' && request('sort') == 'user' ? 'desc' : 'asc'])) }}"
                                    class="flex items-center">
                                    User
                                    @if(request('sort') == 'user')
                                        <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @else
                                        <i class="fas fa-sort ml-1 opacity-50"></i>
                                    @endif
                                </a>
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('admin.bookings.index', array_merge(request()->all(), ['sort' => 'table', 'direction' => request('direction') == 'asc' && request('sort') == 'table' ? 'desc' : 'asc'])) }}"
                                    class="flex items-center">
                                    Meja
                                    @if(request('sort') == 'table')
                                        <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @else
                                        <i class="fas fa-sort ml-1 opacity-50"></i>
                                    @endif
                                </a>
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('admin.bookings.index', array_merge(request()->all(), ['sort' => 'start_time', 'direction' => request('direction') == 'asc' && request('sort') == 'start_time' ? 'desc' : 'asc'])) }}"
                                    class="flex items-center">
                                    Mulai
                                    @if(request('sort') == 'start_time')
                                        <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @else
                                        <i class="fas fa-sort ml-1 opacity-50"></i>
                                    @endif
                                </a>
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('admin.bookings.index', array_merge(request()->all(), ['sort' => 'end_time', 'direction' => request('direction') == 'asc' && request('sort') == 'end_time' ? 'desc' : 'asc'])) }}"
                                    class="flex items-center">
                                    Selesai
                                    @if(request('sort') == 'end_time')
                                        <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @else
                                        <i class="fas fa-sort ml-1 opacity-50"></i>
                                    @endif
                                </a>
                            </th>
                            {{-- <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('admin.bookings.index', array_merge(request()->all(), ['sort' => 'status', 'direction' => request('direction') == 'asc' && request('sort') == 'status' ? 'desc' : 'asc'])) }}"
                                    class="flex items-center">
                                    Status
                                    @if(request('sort') == 'status')
                                    <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }} ml-1"></i>
                                    @else
                                    <i class="fas fa-sort ml-1 opacity-50"></i>
                                    @endif
                                </a>
                            </th> --}}
                            {{-- <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th> --}}
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($bookings as $booking)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <img class="h-10 w-10 rounded-full"
                                                src="https://ui-avatars.com/api/?name={{ urlencode($booking->user->name) }}&color=7F9CF5&background=EBF4FF"
                                                alt="">
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $booking->user->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $booking->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $booking->table->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}</div>
                                    <div class="text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($booking->start_time)->format('d M Y') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($booking->end_time)->format('H:i') }}</div>
                                    <div class="text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($booking->end_time)->format('d M Y') }}</div>
                                </td>
                                {{-- <td class="px-6 py-4 whitespace-nowrap">
                                    @if($booking->status === 'booked')
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <i class="fas fa-clock mr-1"></i> Booked
                                    </span>
                                    @elseif($booking->status === 'selesai')
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-check mr-1"></i> Selesai
                                    </span>
                                    @else
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        <i class="fas fa-times mr-1"></i> Dibatalkan
                                    </span>
                                    @endif
                                </td> --}}
                                {{-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.bookings.show', $booking->id) }}"
                                            class="text-blue-600 hover:text-blue-900" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        @if($booking->status === 'booked')
                                            <a href="{{ route('admin.bookings.edit', $booking->id) }}"
                                                class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <form action="{{ route('admin.bookings.complete', $booking->id) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-green-600 hover:text-green-900" title="Selesai"
                                                    onclick="return confirm('Apakah anda yakin menyelesaikan booking ini?')">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            </form>

                                            <form action="{{ route('admin.bookings.cancel', $booking->id) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-red-600 hover:text-red-900" title="Batalkan"
                                                    onclick="return confirm('Apakah anda yakin membatalkan booking ini?')">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td> --}}
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <i class="fas fa-calendar-times text-4xl text-gray-300 mb-3"></i>
                                        <p class="text-lg font-medium">Belum ada data booking</p>
                                        <p class="text-sm">Coba ubah filter atau tambahkan booking baru</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                <div class="flex flex-col md:flex-row items-center justify-between space-y-2 md:space-y-0">
                    <div class="text-sm text-gray-500">
                        Menampilkan {{ $bookings->firstItem() ?? 0 }} - {{ $bookings->lastItem() ?? 0 }} dari
                        {{ $bookings->total() }} data
                    </div>
                    <div class="flex justify-center">
                        {{ $bookings->appends(request()->query())->onEachSide(1)->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .btn {
                @apply inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs uppercase tracking-wider transition focus:outline-none focus:ring-2 focus:ring-offset-2;
            }

            .btn-primary {
                @apply bg-blue-600 text-white hover:bg-blue-700 focus:ring-blue-500;
            }

            .btn-secondary {
                @apply bg-gray-200 text-gray-700 hover:bg-gray-300 focus:ring-gray-500;
            }

            .btn-success {
                @apply bg-green-600 text-white hover:bg-green-700 focus:ring-green-500;
            }

            .pagination {
                @apply flex rounded-md;
            }

            .page-item {
                @apply -ml-px;
            }

            .page-link {
                @apply relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50;
            }

            .page-item.active .page-link {
                @apply z-10 bg-blue-50 border-blue-500 text-blue-600;
            }

            .page-item.disabled .page-link {
                @apply bg-gray-50 text-gray-500 cursor-not-allowed;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Date range validation
                const dateFrom = document.getElementById('date_from');
                const dateTo = document.getElementById('date_to');

                if (dateFrom && dateTo) {
                    dateFrom.addEventListener('change', function () {
                        dateTo.min = dateFrom.value;
                    });

                    dateTo.addEventListener('change', function () {
                        dateFrom.max = dateTo.value;
                    });
                }
            });
        </script>
    @endpush
@endsection