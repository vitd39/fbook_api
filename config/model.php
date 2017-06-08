<?php

return [
    'book' => [
        'status' => [
            'unavailable' => 0,
            'available' => 1,
        ],
        'fields' => [
            'title',
            'description',
            'author',
            'publish_date',
            'total_page',
            'avg_star',
            'code',
            'count_view',
            'status',
            'created_at'
        ],
    ],
    'book_user' => [
        'status' => [
            'waiting' => 1,
            'reading' => 2,
            'done'    => 3,
        ]
    ],
    'filter_books' => [
        'view' => [
            'key' => 'view',
            'field' => 'count_view',
            'title' => translate('title_key.view')
        ],
        'waiting' => [
            'key' => 'waiting',
            'field' => '',
            'title' => translate('title_key.waiting')
        ],
        'rating' => [
            'key' => 'rating',
            'field' => 'avg_star',
            'title' => translate('title_key.rating')
        ],
        'latest' => [
            'key' => 'latest',
            'field' => 'created_at',
            'title' => translate('title_key.latest')
        ]
    ],
    'filter_type' => [
        'category', 'office'
    ],
    'sort_field' => [
        'rating' => 'avg_star',
        'latest' => 'created_at',
        'view' => 'count_view',
    ],
    'sort_type' => [
        'desc', 'asc'
    ],
    'media_type' => [
        'image' => 0,
        'video' => 1,
    ],
];
