<?php

namespace App\Services;

use App\Models\Booking;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    public function __construct()
    {
        $serverKey = config('midtrans.server_key');
        $clientKey = config('midtrans.client_key');
        $merchantId = config('midtrans.merchant_id');
        $isProduction = config('midtrans.is_production', false);

        // Log semua konfigurasi untuk debugging
        Log::info('Midtrans Configuration:', [
            'server_key' => $serverKey,
            'client_key' => $clientKey,
            'merchant_id' => $merchantId,
            'is_production' => $isProduction
        ]);

        if (empty($serverKey)) {
            throw new \Exception('Midtrans server key is not configured');
        }

        Config::$serverKey = $serverKey;
        Config::$isProduction = $isProduction;
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // Log konfigurasi yang digunakan oleh Midtrans
        Log::info('Midtrans Config:', [
            'server_key' => Config::$serverKey,
            'is_production' => Config::$isProduction,
            'is_sanitized' => Config::$isSanitized,
            'is_3ds' => Config::$is3ds
        ]);
    }

    public function createTransaction(Booking $booking)
    {
        try {
            if (!$booking->user) {
                throw new \Exception('User not found for booking');
            }

            if (empty($booking->total_amount) || $booking->total_amount <= 0) {
                throw new \Exception('Invalid booking amount');
            }

            $params = [
                'transaction_details' => [
                    'order_id' => 'BOOK-' . $booking->id,
                    'gross_amount' => (int) $booking->total_amount,
                ],
                'customer_details' => [
                    'first_name' => $booking->user->name,
                    'email' => $booking->user->email,
                ],
                'item_details' => [
                    [
                        'id' => $booking->table_id,
                        'price' => (int) $booking->total_amount,
                        'quantity' => 1,
                        'name' => 'Booking Meja ' . $booking->table->name,
                    ],
                ],
                'expiry' => [
                    'start_time' => now()->format('Y-m-d H:i:s O'),
                    'unit' => 'hour',
                    'duration' => 24,
                ],
            ];

            Log::info('Creating Midtrans transaction:', [
                'booking_id' => $booking->id,
                'amount' => $booking->total_amount,
                'params' => $params
            ]);

            $snapToken = Snap::getSnapToken($params);
            
            if (empty($snapToken)) {
                throw new \Exception('Empty snap token received from Midtrans');
            }

            Log::info('Midtrans transaction created successfully:', [
                'booking_id' => $booking->id,
                'snap_token' => $snapToken
            ]);

            return $snapToken;
        } catch (\Exception $e) {
            Log::error('Midtrans transaction failed:', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Failed to create Midtrans transaction: ' . $e->getMessage());
        }
    }

    public function handleNotification($notification)
    {
        try {
            $transaction = $notification->transaction_status;
            $type = $notification->payment_type;
            $orderId = $notification->order_id;
            $fraud = $notification->fraud_status;

            Log::info('Received Midtrans notification:', [
                'transaction_status' => $transaction,
                'payment_type' => $type,
                'order_id' => $orderId,
                'fraud_status' => $fraud
            ]);

            // Extract booking ID from order ID (format: BOOK-{id})
            $bookingId = explode('-', $orderId)[1];
            $booking = Booking::findOrFail($bookingId);

            if ($transaction == 'capture') {
                if ($type == 'credit_card') {
                    if ($fraud == 'challenge') {
                        $booking->status = 'pending';
                    } else {
                        $booking->status = 'paid';
                    }
                }
            } else if ($transaction == 'settlement') {
                $booking->status = 'paid';
            } else if ($transaction == 'pending') {
                $booking->status = 'pending';
            } else if ($transaction == 'deny') {
                $booking->status = 'cancelled';
            } else if ($transaction == 'expire') {
                $booking->status = 'expired';
            } else if ($transaction == 'cancel') {
                $booking->status = 'cancelled';
            }

            $booking->payment_id = $notification->transaction_id;
            $booking->payment_method = $type;
            $booking->save();

            Log::info('Booking status updated:', [
                'booking_id' => $booking->id,
                'new_status' => $booking->status
            ]);

            return $booking;
        } catch (\Exception $e) {
            Log::error('Failed to handle Midtrans notification:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
} 