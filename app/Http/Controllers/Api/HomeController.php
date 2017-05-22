<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Repositories\BookRepository;

class HomeController extends ApiController
{
    protected $bookSelect = [
        'id',
        'title',
        'description',
        'author',
        'publish_date',
        'total_page',
        'avg_star',
        'count_view',
        'status',
    ];

    protected $bookRepository;

    public function __construct(BookRepository $bookRepository)
    {
        parent::__construct();
        $this->bookRepository = $bookRepository;
    }

    public function index()
    {
        return $this->getData(function() {
            $this->compacts['items'] = $this->bookRepository->getDataInHomepage(['image'], $this->bookSelect);
        });
    }
}
