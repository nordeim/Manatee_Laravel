<?php

return [
    'products_per_page' => env('THESCENT_PRODUCTS_PER_PAGE', 12),
    'default_currency' => env('THESCENT_DEFAULT_CURRENCY', 'USD'),

    'notifications' => [
        'low_stock_email' => env('THESCENT_LOW_STOCK_EMAIL', 'admin@example.com'),
        'admin_email' => env('THESCENT_ADMIN_EMAIL', 'admin@example.com'), // For general admin notifications
    ],

    'features' => [
        'reviews' => env('THESCENT_FEATURE_REVIEWS', true),
        'wishlist' => env('THESCENT_FEATURE_WISHLIST', false),
        // Add more feature flags as needed
    ],

    'seo' => [
        'default_title' => 'The Scent — Signature Aromatherapy & Wellness',
        'default_description' => 'Experience the essence of nature with our handcrafted aromatherapy blends.',
    ],

    'social_links' => [
        'facebook' => env('SOCIAL_FACEBOOK_URL', '#'),
        'instagram' => env('SOCIAL_INSTAGRAM_URL', '#'),
        'pinterest' => env('SOCIAL_PINTEREST_URL', '#'),
        'twitter' => env('SOCIAL_TWITTER_URL', '#'),
    ],

    'contact' => [
        'address' => '123 Aroma Lane, Wellness City, WC 10001',
        'phone' => '+1 (555) 123-4567',
        'email' => 'care@thescent.com',
    ],
];
