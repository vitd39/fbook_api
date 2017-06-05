<?php

namespace App\Contracts\Repositories;

use App\Eloquent\Book;

interface BookRepository extends AbstractRepository
{
    public function getDataInHomepage($with = [], $dataSelect = ['*']);

    public function getBooksByFields($with = [], $dataSelect = ['*'], $field, $attribute = []);

    public function getDataSearch(array $attribute, $with = [], $dataSelect = ['*']);

    public function booking(Book $book, array $data);

    public function review($bookId, array $data);

    public function getDataFilterInHomepage($with = [], $dataSelect = ['*'], $filters = []);

    public function show($id);
}
