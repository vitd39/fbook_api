<?php

return [
    'action_exception_method' => [
        'store', 'storeMultiple', 'update', 'destroy', 'updateMultiple', 'restore', 'delete'
    ],
    'round_average_star' => 2,
    'default_provider' => 'framgia',
    'book_image_path_default' => 'images/book_default.jpg',
    'image_size' => [
        'thumbnail' => [
            'w' => 100,
            'h' => 100,
            'fit' => 'crop',
        ],
        'small' => [
            'w' => 320,
            'h' => 240,
            'fit' => 'crop',
        ],
        'medium' => [
            'w' => 640,
            'h' => 480,
            'fit' => 'crop',
        ],
        'large' => [
            'w' => 800,
            'h' => 600,
            'fit' => 'crop',
        ],
        'thumbnail_web' => [
            'w' => 150,
            'h' => 200,
            'fit' => 'crop',
        ],
        'small_web' => [
            'w' => 320,
            'h' => 240,
            'fit' => 'crop',
        ],
        'medium_web' => [
            'w' => 640,
            'h' => 480,
            'fit' => 'crop',
        ],
        'large_web' => [
            'w' => 800,
            'h' => 600,
            'fit' => 'crop',
        ]
    ]
];
