<?php

return [
    'merchant_id' => env('MIDTRANS_MERCHANT_ID', ''),
    'client_key' => env('MIDTRANS_CLIENT_KEY', ''),
    'server_key' => env('MIDTRANS_SERVER_KEY', ''),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'snap_url' => env('MIDTRANS_IS_PRODUCTION', false) ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js',
    'notification_url' => env('APP_URL') . '/payment/notification',
    'expiry_duration' => 24, // Durasi expired dalam jam
    'expiry_unit' => 'hour',
]; 