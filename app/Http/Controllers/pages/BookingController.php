<?php

namespace App\Http\Controllers\pages;
use App\Http\Controllers\Controller;

use App\Models\Booking;
use App\Models\Table;
use App\Models\PendingBooking;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class BookingController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    // Tambahkan method baru untuk booking langsung oleh admin
  // Ganti seluruh fungsi adminDirectBooking dengan ini
public function adminDirectBooking($request) {
    try {
        $data = $request instanceof \Illuminate\Http\Request ? $request->all() : $request->toArray();
        
        if (!isset($data['table_id']) || !isset($data['start_time']) || !isset($data['end_time'])) {
            return response()->json(['message' => 'Missing required fields'], 400);
        }

        $user = Auth::user();
        $table = Table::with('venue')->findOrFail($data['table_id']);
        $venue = $table->venue;

        // Validasi otorisasi admin (menggunakan struktur yang konsisten dengan kodemu)
        if ($user->role !== 'admin' || $user->venue_id !== $table->venue_id) {
            return response()->json(['message' => 'Unauthorized action'], 403);
        }

        $startDateTime = Carbon::parse($data['start_time']);
        $endDateTime = Carbon::parse($data['end_time']);

        // --- Validasi jam operasional (logika ini sudah benar) ---
        $operationalDayStart = Carbon::createFromFormat('Y-m-d H:i:s', $startDateTime->format('Y-m-d') . ' ' . $venue->open_time, 'Asia/Jakarta');
        if ($venue->is_overnight && $startDateTime < $operationalDayStart) {
            $operationalDayStart->subDay();
        }

        $operationalDayEnd = $operationalDayStart->copy()->setTimeFromTimeString($venue->close_time);
        if ($venue->is_overnight) {
            $operationalDayEnd->addDay();
        }
        
        if ($startDateTime->lt($operationalDayStart) || $endDateTime->gt($operationalDayEnd)) {
            Log::warning('Admin direct booking attempt outside operational hours.', [
                'start_time' => $startDateTime->toDateTimeString(),
                'venue_open' => $operationalDayStart->toDateTimeString(),
                'venue_close' => $operationalDayEnd->toDateTimeString(),
            ]);
            return response()->json(['message' => 'Waktu booking di luar jam operasional venue.'], 400);
        }
        // --- Akhir Validasi jam operasional ---
        
        // --- PERBAIKAN LOGIKA KONFLIK DIMULAI DI SINI ---
        // Kita hapus ->whereDate() dan langsung cek bentrokan waktu.
        $conflict = Booking::where('table_id', $data['table_id'])
            ->whereIn('status', ['paid', 'pending'])
            ->where(function($query) use ($startDateTime, $endDateTime) {
                // Booking yang baru tidak boleh dimulai di tengah booking lain.
                // Booking yang baru juga tidak boleh berakhir di tengah booking lain.
                // Booking yang baru juga tidak boleh "menelan" booking lain.
                $query->where('start_time', '<', $endDateTime)
                      ->where('end_time', '>', $startDateTime);
            })
            ->exists();

        if ($conflict) {
            return response()->json(['message' => 'Meja sudah dibooking di jam tersebut'], 409);
        }
        // --- AKHIR DARI PERBAIKAN LOGIKA KONFLIK ---

        // Hitung total biaya dan durasi
        $duration = $endDateTime->diffInHours($startDateTime);
        $totalAmount = $duration * $table->price_per_hour;

        $adminOrderId = 'ADMIN-' . $user->id . '-' . time();

        $booking = Booking::create([
            'table_id' => $data['table_id'],
            'user_id' => $user->id,
            'start_time' => $startDateTime,
            'end_time' => $endDateTime,
            'status' => 'paid',
            'total_amount' => $totalAmount,
            'payment_id' => null,
            'payment_method' => 'admin_direct',
            'order_id' => $adminOrderId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil dibuat oleh admin',
            'booking_id' => $booking->id,
            'booking_details' => [
                'table_name' => $table->name,
                'start_time' => $startDateTime->format('Y-m-d H:i:s'),
                'end_time' => $endDateTime->format('Y-m-d H:i:s'),
                'duration' => $duration . ' jam',
                'total_amount' => 'Rp ' . number_format($totalAmount, 0, ',', '.')
            ]
        ]);

    } catch (\Exception $e) {
        \Log::error('Admin direct booking error:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
            'request_data' => $request instanceof \Illuminate\Http\Request ? $request->all() : $request->toArray()
        ]);

        return response()->json(['success' => false, 'message' => 'Gagal membuat booking: ' . $e->getMessage()], 500);
    }
}


    public function createPaymentIntent(Request $request) {
    try {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'start_time' => 'required',
            'duration' => 'required|integer|min:1|max:12',
            'booking_date' => 'required|date_format:Y-m-d',
        ]);

        $user = Auth::user();
        $table = Table::with('venue')->findOrFail($request->table_id);
        $venue = $table->venue;

        $bookingDate = $request->booking_date;
        $startTimeString = $request->start_time;
        $duration = (int) $request->duration;

        // 1. Hitung start & end time yang sebenarnya
        $startDateTime = Carbon::createFromFormat('Y-m-d H:i', $bookingDate . ' ' . $startTimeString, 'Asia/Jakarta');

        // --- AWAL PERBAIKAN LOGIKA STRING COMPARISON ---
        $startTimeObject = Carbon::createFromFormat('H:i', $startTimeString);
        $openTimeObject = Carbon::parse($venue->open_time);

        // Bandingkan sebagai objek Carbon, bukan string
        if ($venue->is_overnight && $startTimeObject->lt($openTimeObject)) {
            $startDateTime->addDay();
        }
        $endDateTime = $startDateTime->copy()->addHours($duration);

        // 2. --- BLOK VALIDASI YANG DIPERBAIKI ---
        $operationalDayStart = Carbon::createFromFormat('Y-m-d H:i:s', $startDateTime->format('Y-m-d') . ' ' . $venue->open_time, 'Asia/Jakarta');
        if ($venue->is_overnight && $startDateTime < $operationalDayStart) {
            $operationalDayStart->subDay();
        }
        $operationalDayEnd = $operationalDayStart->copy()->setTimeFromTimeString($venue->close_time);
        if ($venue->is_overnight) {
            $operationalDayEnd->addDay();
        }
        if ($startDateTime->lt($operationalDayStart) || $endDateTime->gt($operationalDayEnd)) {
            Log::warning('Booking attempt outside operational hours.', [
                'start_time' => $startDateTime->toDateTimeString(), 'end_time' => $endDateTime->toDateTimeString(),
                'venue_open' => $operationalDayStart->toDateTimeString(), 'venue_close' => $operationalDayEnd->toDateTimeString(),
            ]);
            return response()->json(['success' => false, 'message' => 'Durasi booking di luar jam operasional venue.'], 422);
        }
        // --- AKHIR DARI BLOK VALIDASI ---

        // 3. Cek untuk admin direct booking (tidak berubah)
        if ($user->role === 'admin' && $user->venue_id === $table->venue_id) {
            return $this->adminDirectBooking(collect([
                'table_id' => $request->table_id,
                'start_time' => $startDateTime->toDateTimeString(),
                'end_time' => $endDateTime->toDateTimeString(),
            ]));
        }

        // 4. Cek konflik booking (tidak berubah)
        $conflict = Booking::where('table_id', $request->table_id)
            ->where('status', 'paid')
            ->where(function($query) use ($startDateTime, $endDateTime) {
                $query->where('start_time', '<', $endDateTime)
                      ->where('end_time', '>', $startDateTime);
            })
            ->exists();
        if ($conflict) {
            return response()->json(['success' => false, 'message' => 'Meja sudah dibooking di jam tersebut'], 409);
        }

        // 5. Proses ke Midtrans (tidak berubah)
        $totalAmount = $duration * $table->price_per_hour;
        $tempOrderId = 'TEMP-' . Auth::id() . '-' . time();

        PendingBooking::updateOrCreate(
            ['user_id' => Auth::id(), 'table_id' => $request->table_id, 'start_time' => $startDateTime->toDateTimeString()],
            ['end_time' => $endDateTime->toDateTimeString(), 'total_amount' => $totalAmount, 'order_id' => $tempOrderId, 'expired_at' => now()->addHours(24) ]
        );

        $snapToken = $this->midtransService->createTemporaryTransaction($table, $totalAmount, $tempOrderId, Auth::user());
        if (!$snapToken) {
            throw new \Exception('Failed to get snap token from Midtrans');
        }

        return response()->json([
            'success' => true,
            'snap_token' => $snapToken,
            'order_id' => $tempOrderId
        ]);
    } catch (\Exception $e) {
        \Log::error('Payment intent error:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Gagal membuat transaksi: ' . $e->getMessage()
        ], 500);
    }
}

    public function store(Request $request) {
        try {
            $request->validate([
                'order_id' => 'required|string',
                'transaction_id' => 'required|string',
                'payment_method' => 'required|string',
                'transaction_status' => 'required|string',
            ]);

            // Retrieve booking data from session
            $tempBooking = Session::get('temp_booking');
            $tempOrderId = Session::get('temp_order_id');

            // If not in session, try from pending bookings
            if (!$tempBooking || $tempOrderId != $request->order_id) {
                $pendingBooking = PendingBooking::where('order_id', $request->order_id)
                    ->where('user_id', Auth::id())
                    ->first();
                
                if (!$pendingBooking) {
                    throw new \Exception('Invalid or expired booking session');
                }

                $tempBooking = [
                    'table_id' => $pendingBooking->table_id,
                    'user_id' => $pendingBooking->user_id,
                    'start_time' => $pendingBooking->start_time,
                    'end_time' => $pendingBooking->end_time,
                    'total_amount' => $pendingBooking->total_amount,
                ];
                $tempOrderId = $pendingBooking->order_id;
            }

            // Process based on transaction status
            if ($request->transaction_status == 'settlement' || $request->transaction_status == 'capture') {
                // Create the actual booking record
                $booking = Booking::create([
                    'table_id' => $tempBooking['table_id'],
                    'user_id' => $tempBooking['user_id'],
                    'start_time' => $tempBooking['start_time'],
                    'end_time' => $tempBooking['end_time'],
                    'status' => 'paid',
                    'total_amount' => $tempBooking['total_amount'],
                    'payment_id' => $request->transaction_id,
                    'payment_method' => $request->payment_method,
                    'order_id' => $request->order_id,
                ]);

                // Update table status to booked
                $table = Table::findOrFail($tempBooking['table_id']);
                $table->update(['status' => 'Booked']);

                // Delete pending booking if exists
                PendingBooking::where('order_id', $request->order_id)->delete();

                // Clear session data
                Session::forget('temp_booking');
                Session::forget('temp_order_id');

                return response()->json([
                    'message' => 'Booking created successfully',
                    'booking_id' => $booking->id
                ]);
            } else {
                // For pending, deny, cancel, etc. - don't create booking
                return response()->json([
                    'message' => 'Payment ' . $request->transaction_status . ', booking not created',
                    'status' => $request->transaction_status
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Booking store error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Failed to create booking: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getBookedSchedules(Request $request) {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'date' => 'required|date',
        ]);

        $table = Table::with('venue')->findOrFail($request->table_id);
    $venue = $table->venue;
    $requestDate = Carbon::parse($request->date);

        // Only get bookings with paid status
        $query = Booking::where('table_id', $request->table_id)
                    ->where('status', 'paid');

            if ($venue->is_overnight) {
                // Jika overnight, ambil booking dari jam buka di hari H
                // sampai jam tutup di hari H+1
                $startOperationalDay = $requestDate->copy()->setTimeFromTimeString($venue->open_time);
                $endOperationalDay = $requestDate->copy()->addDay()->setTimeFromTimeString($venue->close_time);

                $query->whereBetween('start_time', [$startOperationalDay, $endOperationalDay]);

            } else {
                // Jika tidak overnight, ambil booking hanya di hari H
                $query->whereDate('start_time', $requestDate);
            }

             $bookings = $query->select('start_time', 'end_time')
        ->get()
        ->map(function ($booking) {
            return [
                // Format H:i tetap sama, karena frontend hanya butuh jamnya
                'start' => Carbon::parse($booking->start_time)->format('H:i'),
                'end' => Carbon::parse($booking->end_time)->format('H:i')
            ];
        });


        return response()->json($bookings);
    }

    public function handleNotification(Request $request)
    {
        try {
            $notification = $request->all();
            Log::info('Midtrans notification received:', $notification);

            $transactionStatus = $notification['transaction_status'];
            $orderId = $notification['order_id'];
            $fraudStatus = $notification['fraud_status'] ?? null;
            $transactionId = $notification['transaction_id'];
            $paymentType = $notification['payment_type'];

            // Check if this is a temporary order (from our new flow)
            if (strpos($orderId, 'TEMP-') === 0) {
                // This is a notification for a transaction that started with our new flow
                // We don't need to do anything here as the frontend will handle creating the booking
                // after successful payment via the store method
                Log::info('Received notification for temp order, will be handled by frontend', [
                    'order_id' => $orderId
                ]);
                return response()->json(['message' => 'Notification received for temp order']);
            }

            // Handle notifications for existing bookings (from old flow or admin-created bookings)
            $booking = Booking::where('order_id', $orderId)->first();
            if (!$booking) {
                Log::error('Booking not found for order_id: ' . $orderId);
                return response()->json(['message' => 'Booking not found'], 404);
            }

            // Update booking status based on transaction status
            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'challenge') {
                    $booking->status = 'challenge';
                } else if ($fraudStatus == 'accept') {
                    $booking->status = 'paid';
                    // Update table status to booked
                    $booking->table->update(['status' => 'Booked']);
                }
            } else if ($transactionStatus == 'settlement') {
                $booking->status = 'paid';
                // Update table status to booked
                $booking->table->update(['status' => 'Booked']);
            } else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
                $booking->status = 'cancelled';
                // Reset table status to available if no other active bookings
                $hasActiveBookings = $booking->table->bookings()
                    ->where('status', 'paid')
                    ->where('id', '!=', $booking->id)
                    ->exists();
                if (!$hasActiveBookings) {
                    $booking->table->update(['status' => 'Available']);
                }
            } else if ($transactionStatus == 'pending') {
                $booking->status = 'pending';
            }

            $booking->payment_id = $transactionId;
            $booking->payment_method = $paymentType;
            $booking->save();
            
            Log::info('Booking status updated:', ['booking_id' => $booking->id, 'status' => $booking->status]);

            return response()->json(['message' => 'Notification processed successfully']);
        } catch (\Exception $e) {
            Log::error('Error processing Midtrans notification: ' . $e->getMessage());
            return response()->json(['message' => 'Error processing notification'], 500);
        }
    }

    public function getPendingBookings()
    {
        $pendingBookings = PendingBooking::where('user_id', Auth::id())
            ->where('expired_at', '>', now())
            ->with(['table.venue'])
            ->get();

        return response()->json($pendingBookings);
    }

    public function resumeBooking($id)
    {
        try {
            $pendingBooking = PendingBooking::where('id', $id)
                ->where('user_id', Auth::id())
                ->where('expired_at', '>', now())
                ->firstOrFail();

            // Cek apakah meja masih available di waktu tersebut
            $conflict = Booking::where('table_id', $pendingBooking->table_id)
                ->where(function($query) use ($pendingBooking) {
                    $query->whereBetween('start_time', [$pendingBooking->start_time, $pendingBooking->end_time])
                    ->orWhere(function($query) use ($pendingBooking) {
                        $query->where('start_time', '<', $pendingBooking->start_time)
                        ->where('end_time', '>', $pendingBooking->start_time);
                    });
                })
                ->where('status', 'paid')
                ->exists();

            if ($conflict) {
                return response()->json([
                    'success' => false,
                    'message' => 'Meja ini sudah tidak tersedia pada waktu yang Anda pilih'
                ], 409);
            }

            // Simpan ke session
            Session::put('temp_booking', [
                'table_id' => $pendingBooking->table_id,
                'user_id' => Auth::id(),
                'start_time' => $pendingBooking->start_time,
                'end_time' => $pendingBooking->end_time,
                'total_amount' => $pendingBooking->total_amount,
                'created_at' => now(),
            ]);
            Session::put('temp_order_id', $pendingBooking->order_id);

            // Dapatkan table data
            $table = Table::findOrFail($pendingBooking->table_id);

            // Dapatkan snap token baru dari Midtrans
            $snapToken = $this->midtransService->createTemporaryTransaction(
                $table, 
                $pendingBooking->total_amount, 
                $pendingBooking->order_id, 
                Auth::user()
            );

            if (!$snapToken) {
                throw new \Exception('Failed to get snap token from Midtrans');
            }

            return response()->json([
                'success' => true,
                'message' => 'Booking dapat dilanjutkan',
                'snap_token' => $snapToken,
                'order_id' => $pendingBooking->order_id,
                'venue_id' => $pendingBooking->table->venue_id,
                'table_id' => $pendingBooking->table_id,
                'table_name' => $pendingBooking->table->name,
                'start_time' => Carbon::parse($pendingBooking->start_time)->format('H:i'),
                'duration' => Carbon::parse($pendingBooking->start_time)->diffInHours($pendingBooking->end_time),
                'total_amount' => $pendingBooking->total_amount
            ]);

        } catch (\Exception $e) {
            Log::error('Resume booking error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deletePendingBooking($id)
    {
        try {
            $pendingBooking = PendingBooking::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();
            
            $pendingBooking->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Booking berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus booking: ' . $e->getMessage()
            ], 500);
        }
    }

    // GANTI SELURUH FUNGSI showReschedule DENGAN YANG INI

public function showReschedule($id)
{
    $booking = Booking::with(['table.venue', 'table.venue.tables'])->findOrFail($id);
    
    // Validasi kepemilikan dan status booking (tidak ada perubahan)
    if ($booking->user_id !== auth()->id() || $booking->status !== 'paid' || $booking->reschedule_count >= 1) {
        return redirect()->route('booking.history')->with('error', 'Batas maksimal reschedule telah digunakan (1x).');
    }
    
    $rescheduleDeadline = Carbon::parse($booking->start_time)->subHour();
    if (now() > $rescheduleDeadline) {
        return redirect()->route('booking.history')->with('error', 'Batas waktu reschedule telah berakhir (1 jam sebelum mulai).');
    }
    
    $venue = $booking->table->venue;
    $duration = Carbon::parse($booking->start_time)->diffInHours($booking->end_time);

    // --- AWAL LOGIKA BARU UNTUK MENENTUKAN TANGGAL OPERASIONAL ---
    $startTime = Carbon::parse($booking->start_time);
    $operational_date = $startTime->copy(); // Mulai dengan tanggal kalender

    // Jika venue-nya overnight DAN jam booking lebih pagi dari jam buka,
    // maka tanggal operasionalnya adalah H-1 dari tanggal kalender.
    if ($venue->is_overnight && $startTime->format('H:i:s') < $venue->open_time) {
        $operational_date->subDay();
    }
    
    // Ubah ke format Y-m-d untuk dikirim ke view
    $operational_date_string = $operational_date->format('Y-m-d');
    // --- AKHIR DARI LOGIKA BARU ---

    // Kirim $operational_date_string ke view, bukan lagi tanggal dari $booking
    return view('pages.reschedule', compact('booking', 'venue', 'duration', 'operational_date_string'));
}

    /**
     * Process a reschedule request.
     */
    public function processReschedule(Request $request, $id)
    {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'start_time' => 'required|date_format:Y-m-d H:i:s',
            'end_time' => 'required|date_format:Y-m-d H:i:s|after:start_time',
        ]);
        
        $booking = Booking::findOrFail($id);
        
        // Perform validation
        if ($booking->user_id !== auth()->id() || 
            $booking->start_time <= now() || 
            $booking->status !== 'paid' || 
            now() > Carbon::parse($booking->start_time)->subHour()) {
            return response()->json([
                'success' => false,
                'message' => 'Booking ini tidak dapat di-reschedule.'
            ], 422);
        }
        
        // Check if the selected time is available (exclude current booking when checking conflicts)
        $existingBookings = Booking::where('table_id', $request->table_id)
            ->where('id', '!=', $booking->id)
            ->where('status', 'paid')
            ->where(function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('start_time', '<', $request->end_time)
                      ->where('end_time', '>', $request->start_time);
                });
            })->count();
        
        if ($existingBookings > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Jam yang dipilih sudah dibooking oleh orang lain.'
            ], 422);
        }
        
        // Update the booking with new schedule
        $booking->start_time = $request->start_time;
        $booking->end_time = $request->end_time;
        $booking->table_id = $request->table_id;
        $booking->save();

        // Increment reschedule count
        $booking->increment('reschedule_count');
        
        return response()->json([
            'success' => true,
            'message' => 'Booking berhasil di-reschedule.',
            'redirect' => route('booking.history')
        ]);
    }

    /**
     * Check availability for reschedule.
     */
    public function checkRescheduleAvailability(Request $request)
{
    $request->validate([
        'table_id' => 'required|exists:tables,id',
        'date' => 'required|date_format:Y-m-d',
        'booking_id' => 'required|exists:bookings,id'
    ]);
    
    $table = Table::with('venue')->findOrFail($request->table_id);
    $venue = $table->venue;
    $requestDate = Carbon::parse($request->date);
    
    // Query untuk mengambil booking lain di meja yang sama
    $query = Booking::where('table_id', $table->id)
        ->where('id', '!=', $request->booking_id) // Jangan ikut sertakan booking yang sedang di-reschedule
        ->where('status', 'paid');

    // --- LOGIKA OVERNIGHT DITERAPKAN DI SINI ---
    if ($venue->is_overnight) {
        // Ambil booking dari jam buka di hari H sampai jam tutup di hari H+1
        $startOperationalDay = $requestDate->copy()->setTimeFromTimeString($venue->open_time);
        $endOperationalDay = $requestDate->copy()->addDay()->setTimeFromTimeString($venue->close_time);

        $query->whereBetween('start_time', [$startOperationalDay, $endOperationalDay]);
    } else {
        // Logika standar untuk venue yang tidak overnight
        $query->whereDate('start_time', $requestDate);
    }
    // --- AKHIR DARI LOGIKA OVERNIGHT ---

    $bookings = $query->get(['start_time', 'end_time'])
        ->map(function ($booking) {
            return [
                'start' => Carbon::parse($booking->start_time)->format('H:i'),
                'end' => Carbon::parse($booking->end_time)->format('H:i'),
            ];
        });
    
    return response()->json($bookings);
}
}