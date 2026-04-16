<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Booking Configuration
    |--------------------------------------------------------------------------
    |
    | Configurazione del sistema di prenotazione Solarya Travel
    |
    */

    // Prenotazione
    'pending_expiry_minutes' => env('BOOKING_PENDING_EXPIRY_MINUTES', 30),
    'advance_booking_hours' => env('BOOKING_ADVANCE_HOURS', 24),
    'max_booking_days_ahead' => env('BOOKING_MAX_DAYS_AHEAD', 180),
    'allow_same_day_booking' => env('BOOKING_ALLOW_SAME_DAY', true),

    // Prezzi
    'tax_rate' => env('BOOKING_TAX_RATE', 0.22),
    'default_currency' => 'EUR',

    // QR Code
    'qr_code' => [
        'size' => env('QR_CODE_SIZE', 300),
        'storage_disk' => env('QR_CODE_STORAGE', 'public'),
        'storage_path' => 'qrcodes',
    ],

    // Notifiche
    'notifications' => [
        'reminder_hours_before' => env('NOTIFICATION_REMINDER_HOURS', 24),
        'review_request_delay_hours' => env('NOTIFICATION_REVIEW_DELAY', 24),
    ],

    // Formati
    'date_format' => 'd/m/Y',
    'time_format' => 'H:i',
    'datetime_format' => 'd/m/Y H:i',

    // Prefix numeri prenotazione
    'booking_number_prefix' => 'SLY',
];
