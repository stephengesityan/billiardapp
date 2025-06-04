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
   public function adminDirectBooking($request) {
    try {
        // Handle both Request object dan Collection
        $data = $request instanceof \Illuminate\Http\Request ? $request->all() : $request->toArray();
        
        // Validasi manual karena bisa dari collection
        if (!isset($data['table_id']) || !isset($data['start_time']) || !isset($data['end_time'])) {
            return response()->json([
                'message' => 'Missing required fields'
            ], 400);
        }

        $user = Auth::user();

        // Validasi bahwa user adalah admin dan mengelola venue dari meja tersebut
        $table = Table::findOrFail($data['table_id']);
        if ($user->role !== 'admin' || $user->venue_id !== $table->venue_id) {
            return response()->json([
                'message' => 'Unauthorized action'
            ], 403);
        }

        // Parse start_time dan end_time yang sudah dalam format datetime string
        $startDateTime = Carbon::parse($data['start_time']);
        $endDateTime = Carbon::parse($data['end_time']);

        // Validasi jam operasional venue (opsional, karena sudah divalidasi di createPaymentIntent)
        $venue = $table->venue;
        $venueOpenTime = Carbon::parse($venue->open_time);
        $venueCloseTime = Carbon::parse($venue->close_time);
        
        $startTimeOnly = $startDateTime->format('H:i');
        $endTimeOnly = $endDateTime->format('H:i');
        
        if ($startTimeOnly < $venueOpenTime->format('H:i') || $endTimeOnly > $venueCloseTime->format('H:i')) {
            return response()->json([
                'message' => 'Waktu booking di luar jam operasional venue'
            ], 400);
        }

        // Cek konflik booking
        $conflict = Booking::where('table_id', $data['table_id'])
            ->whereDate('start_time', $startDateTime->format('Y-m-d'))
            ->where(function($query) use ($startDateTime, $endDateTime) {
                $query->where(function($q) use ($startDateTime, $endDateTime) {
                    // Case 1: Booking baru mulai di tengah booking yang ada
                    $q->where('start_time', '<=', $startDateTime)
                      ->where('end_time', '>', $startDateTime);
                })->orWhere(function($q) use ($startDateTime, $endDateTime) {
                    // Case 2: Booking baru berakhir di tengah booking yang ada
                    $q->where('start_time', '<', $endDateTime)
                      ->where('end_time', '>=', $endDateTime);
                })->orWhere(function($q) use ($startDateTime, $endDateTime) {
                    // Case 3: Booking baru mencakup seluruh booking yang ada
                    $q->where('start_time', '>=', $startDateTime)
                      ->where('end_time', '<=', $endDateTime);
                });
            })
            ->whereIn('status', ['paid', 'pending'])
            ->exists();

        if ($conflict) {
            return response()->json([
                'message' => 'Meja sudah dibooking di jam tersebut'
            ], 409);
        }

        // Hitung total biaya dan durasi
        $duration = $endDateTime->diffInHours($startDateTime);
        $totalAmount = $duration * $table->price_per_hour;

        // Generate order ID unik untuk admin
        $adminOrderId = 'ADMIN-' . $user->id . '-' . time();

        // Buat booking langsung dengan status paid
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

        return response()->json([
            'success' => false,
            'message' => 'Gagal membuat booking: ' . $e->getMessage()
        ], 500);
    }
}


    public function createPaymentIntent(Request $request) {
    try {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'start_time' => 'required', // Ubah dari date menjadi string untuk format H:i
            'duration' => 'required|integer|min:1|max:12', // Validasi durasi
            'booking_date' => 'required|date_format:Y-m-d', // Validasi tanggal booking
        ]);

        $user = Auth::user();
        $table = Table::with('venue')->findOrFail($request->table_id);

        // Buat datetime lengkap dari booking_date dan start_time
        $bookingDate = $request->booking_date;
        $startTime = $request->start_time; // Format H:i (contoh: "14:00")
        $duration = (int) $request->duration;

        // Gabungkan tanggal dan waktu untuk membuat datetime lengkap
        $startDateTime = Carbon::createFromFormat('Y-m-d H:i', $bookingDate . ' ' . $startTime, 'Asia/Jakarta');
        $endDateTime = $startDateTime->copy()->addHours($duration);

        // Validasi waktu booking dalam jam operasional venue
        $venueOpenTime = Carbon::createFromFormat('H:i:s', $table->venue->open_time)->format('H:i');
        $venueCloseTime = Carbon::createFromFormat('H:i:s', $table->venue->close_time)->format('H:i');
        
        if ($startTime < $venueOpenTime || $startTime >= $venueCloseTime) {
            return response()->json([
                'success' => false,
                'message' => 'Waktu booking di luar jam operasional venue'
            ], 422);
        }

        // Validasi bahwa end time tidak melebihi jam tutup venue
        if ($endDateTime->format('H:i') > $venueCloseTime) {
            return response()->json([
                'success' => false,
                'message' => 'Durasi booking melebihi jam tutup venue'
            ], 422);
        }

        // Cek untuk admin direct booking
        if ($user->role === 'admin' && $user->venue_id === $table->venue_id) {
            return $this->adminDirectBooking(collect([
                'table_id' => $request->table_id,
                'start_time' => $startDateTime->toDateTimeString(),
                'end_time' => $endDateTime->toDateTimeString(),
            ]));
        }

        // Cek konflik booking dengan format datetime lengkap
        $conflict = Booking::where('table_id', $request->table_id)
                ->where(function($query) use ($startDateTime, $endDateTime) {
                    $query->where(function($q) use ($startDateTime, $endDateTime) {
                        $q->where('start_time', '<', $endDateTime)
                          ->where('end_time', '>', $startDateTime);
                    });
                })
                ->where('status', 'paid')
                ->exists();

        if ($conflict) {
            return response()->json([
                'success' => false,
                'message' => 'Meja sudah dibooking di jam tersebut'
            ], 409);
        }

        // Hitung total biaya
        $totalAmount = $duration * $table->price_per_hour;

        // Simpan data booking sementara di session
        Session::put('temp_booking', [
            'table_id' => $request->table_id,
            'user_id' => Auth::id(),
            'start_time' => $startDateTime->toDateTimeString(),
            'end_time' => $endDateTime->toDateTimeString(),
            'total_amount' => $totalAmount,
            'created_at' => now(),
        ]);

        // Generate unique order ID
        $tempOrderId = 'TEMP-' . Auth::id() . '-' . time();
        Session::put('temp_order_id', $tempOrderId);

        // Simpan booking sementara ke database
        PendingBooking::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'table_id' => $request->table_id,
                'start_time' => $startDateTime->toDateTimeString()
            ],
            [
                'end_time' => $endDateTime->toDateTimeString(),
                'total_amount' => $totalAmount,
                'order_id' => $tempOrderId,
                'expired_at' => now()->addHours(24),
            ]
        );

        // Dapatkan snap token dari Midtrans
        $snapToken = $this->midtransService->createTemporaryTransaction($table, $totalAmount, $tempOrderId, Auth::user());

        if (!$snapToken) {
            throw new \Exception('Failed to get snap token from Midtrans');
        }

        \Log::info('Payment intent created successfully:', [
            'order_id' => $tempOrderId,
            'snap_token' => $snapToken,
            'start_time' => $startDateTime->toDateTimeString(),
            'end_time' => $endDateTime->toDateTimeString()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Payment intent created, proceed to payment',
            'total_amount' => $totalAmount,
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

        // Only get bookings with paid status
        $bookings = Booking::where('table_id', $request->table_id)
            ->whereDate('start_time', $request->date)
            ->where('status', 'paid') // Only include paid bookings
            ->select('start_time', 'end_time')
            ->get()
            ->map(function ($booking) {
                return [
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

    public function showReschedule($id)
    {
        $booking = Booking::with(['table.venue', 'table.venue.tables'])->findOrFail($id);
        
        // Check if user owns this booking
        if ($booking->user_id !== auth()->id()) {
            return redirect()->route('booking.history')->with('error', 'Anda tidak memiliki akses ke booking ini.');
        }
        
        // Check if booking is upcoming
        if ($booking->start_time <= now() || $booking->status !== 'paid') {
            return redirect()->route('booking.history')->with('error', 'Booking ini tidak dapat di-reschedule.');
        }

        // Check if booking has reached reschedule limit
        if ($booking->reschedule_count >= 1) {
            return redirect()->route('booking.history')->with('error', 'Booking ini sudah pernah di-reschedule sebelumnya dan tidak dapat di-reschedule lagi.');
        }
        
        // Check if it's within the time limit (at least 1 hour before start)
        $rescheduleDeadline = Carbon::parse($booking->start_time)->subHour();
        if (now() > $rescheduleDeadline) {
            return redirect()->route('booking.history')->with('error', 'Batas waktu reschedule telah berakhir (1 jam sebelum mulai).');
        }
        
        // Get venue and tables data
        $venue = $booking->table->venue;
        
        // Duration in hours
        $duration = Carbon::parse($booking->start_time)->diffInHours($booking->end_time);
        
        return view('pages.reschedule', compact('booking', 'venue', 'duration'));
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
        
        $date = $request->date;
        $tableId = $request->table_id;
        $bookingId = $request->booking_id;
        
        // Get all bookings for this table on this date (excluding the current booking)
        $bookings = Booking::where('table_id', $tableId)
            ->where('id', '!=', $bookingId)
            ->where('status', 'paid')
            ->whereDate('start_time', $date)
            ->get(['start_time', 'end_time'])
            ->map(function ($booking) {
                return [
                    'start' => Carbon::parse($booking->start_time)->format('H:i'),
                    'end' => Carbon::parse($booking->end_time)->format('H:i'),
                ];
            });
        
        return response()->json($bookings);
    }
}