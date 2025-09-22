<?php
return [
    'name' => 'Sip & Serve',
    'manifest' => [
        'name' => 'L PRIMERO CAFE - SIP & SERVE',
        'short_name' => 'POS',
        'start_url' => '/admin',  // Filament admin URL
        'background_color' => '#ffffff',
        'theme_color' => '#f2ca48ff',
        'display' => 'standalone',
        'orientation' => 'portrait',
        'status_bar' => 'default',
        'icons' => [
            '72x72' => '/images/icons/icon-72x72.png',
            '96x96' => '/images/icons/icon-96x96.png',
            '128x128' => '/images/icons/icon-128x128.png',
            '144x144' => '/images/icons/icon-144x144.png',
            '152x152' => '/images/icons/icon-152x152.png',
            '192x192' => '/images/icons/icon-192x192.png',
            '384x384' => '/images/icons/icon-384x384.png',
            '512x512' => '/images/icons/icon-512x512.png',
        ],
        'splash' => [
            '640x1136' => '/images/icons/splash-640x1136.png',
            '750x1334' => '/images/icons/splash-750x1334.png',
            '828x1792' => '/images/icons/splash-828x1792.png',
            '1125x2436' => '/images/icons/splash-1125x2436.png',
        ],
        'shortcuts' => [
            [
                'name' => 'New Sale',
                'description' => 'Start a new sale transaction',
                'url' => '/admin/sales/create',
                'icons' => [
                    'src' => '/images/icons/sale-icon.png',
                    'purpose' => 'any'
                ]
            ],
            [
                'name' => 'Products',
                'description' => 'Manage products',
                'url' => '/admin/products',
                'icons' => [
                    'src' => '/images/icons/product-icon.png',
                    'purpose' => 'any'
                ]
            ],
            [
                'name' => 'Reports',
                'description' => 'View sales reports',
                'url' => '/admin/reports',
                'icons' => [
                    'src' => '/images/icons/report-icon.png',
                    'purpose' => 'any'
                ]
            ]
        ],
        'custom' => []
    ]
];