<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Repositories\BookRepository;

class BookController extends ApiController
{
    public function __construct(BookRepository $repository)
    {
        parent::__construct($repository);
    }
}
