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
    'filter_books' => [
        'view', 'waiting', 'rating', 'latest'
    ],
    'filter_type' => [
        'category', 'office'
    ],
    'sort_field' => [
        'rating' => 'avg_star',
        'latest' => 'created_at',
        'view' => 'count_view',
        'title' => 'title',
    ],
    'sort_type' => [
        'desc', 'asc'
    ],
    'status_book_user' => [
        'waiting' => 1,
        'reading' => 2,
        'done'    => 3,
    ],
];
