<?php

return [
    'book' => [
        'status' => ['available', 'no available'],
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
];
