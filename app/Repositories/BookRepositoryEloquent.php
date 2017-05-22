<?php

namespace App\Repositories;

use App\Contracts\Repositories\BookRepository;

class BookRepositoryEloquent extends AbstractRepositoryEloquent implements BookRepository
{
    public function model()
    {
        return new \App\Eloquent\Book;
    }
}
