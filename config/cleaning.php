<?php

return [
    'name' => 'Cleaning and Janitorial Service Management',
    'short_name' => 'CleanOps',
    'tagline' => 'Enterprise cleaning operations, scheduling, and quality assurance',

    'branding' => [
        'primary' => '#0B1F3A',
        'secondary' => '#0D9488',
        'accent' => '#059669',
        'background' => '#F1F5F9',
        'surface' => '#FFFFFF',
        'text' => '#0F172A',
        'warning' => '#D97706',
        'danger' => '#DC2626',
    ],

    'inspection_thresholds' => [
        'excellent' => 90,
        'good' => 80,
        'needs_improvement' => 70,
        'failed' => 69,
    ],

    'locales' => ['en', 'bn', 'ar', 'hi', 'ur', 'zh'],
    'default_locale' => 'en',
    'rtl_locales' => ['ar', 'ur'],

    'currencies' => ['USD', 'EUR', 'GBP', 'BDT', 'INR', 'AED', 'SAR'],
    'default_currency' => 'USD',

    'booking_reminder_hours' => [24, 2],
    'contract_renewal_days' => 30,
    'certification_expiry_days' => 30,
    'low_stock_default_threshold' => 10,

    'demo_password' => env('DEMO_PASSWORD', 'Demo@12345'),
];