<?php

namespace App\Http\Controllers\pages;
use App\Http\Controllers\Controller;

use App\Models\Booking;
use App\Models\Table;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    protected $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    public function store(Request $request) {
        try {
            $request->validate([
                'table_id' => 'required|exists:tables,id',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time',
            ]);

            // Cek apakah meja sedang dibooking pada waktu tersebut
            $conflict = Booking::where('table_id', $request->table_id)
                    ->where(function($query) use ($request) {
                        $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                        ->orWhere(function($query) use ($request) {
                            $query->where('start_time', '<', $request->start_time)
                            ->where('end_time', '>', $request->start_time);
                        });
                    })
                    ->where('status', '!=', 'cancelled')
                    ->where('status', '!=', 'expired')
                    ->exists();

            if ($conflict) {
                return response()->json(['message' => 'Meja sudah dibooking di jam tersebut'], 409);
            }

            // Hitung total biaya
            $table = Table::findOrFail($request->table_id);
            $startTime = Carbon::parse($request->start_time);
            $endTime = Carbon::parse($request->end_time);
            $duration = $endTime->diffInHours($startTime);
            $totalAmount = $duration * $table->price_per_hour;

            // Buat booking dengan status pending
            $booking = Booking::create([
                'table_id' => $request->table_id,
                'user_id' => Auth::id(),
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'status' => 'pending',
                'total_amount' => $totalAmount,
                'payment_expired_at' => now()->addHours(24), // Expired dalam 24 jam
            ]);

            // Dapatkan snap token dari Midtrans
            $snapToken = $this->midtransService->createTransaction($booking);

            if (!$snapToken) {
                throw new \Exception('Failed to get snap token from Midtrans');
            }

            \Log::info('Booking created successfully:', [
                'booking_id' => $booking->id,
                'snap_token' => $snapToken
            ]);

            return response()->json([
                'message' => 'Booking berhasil dibuat, silahkan lakukan pembayaran',
                'booking_id' => $booking->id,
                'total_amount' => $totalAmount,
                'snap_token' => $snapToken
            ]);
        } catch (\Exception $e) {
            \Log::error('Booking error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if (isset($booking)) {
                $booking->delete(); // Hapus booking jika gagal membuat transaksi
            }

            return response()->json([
                'message' => 'Gagal membuat transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getBookedSchedules(Request $request) {
        $request->validate([
            'table_id' => 'required|exists:tables,id',
            'date' => 'required|date',
        ]);

        $bookings = Booking::where('table_id', $request->table_id)
            ->whereDate('start_time', $request->date)
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'expired')
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
            $fraudStatus = $notification['fraud_status'];

            // Get booking from order_id
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

            $booking->save();
            Log::info('Booking status updated:', ['booking_id' => $booking->id, 'status' => $booking->status]);

            return response()->json(['message' => 'Notification processed successfully']);
        } catch (\Exception $e) {
            Log::error('Error processing Midtrans notification: ' . $e->getMessage());
            return response()->json(['message' => 'Error processing notification'], 500);
        }
    }
}
