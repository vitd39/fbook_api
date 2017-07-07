<?php

namespace App\Contracts\Repositories;

use App\Eloquent\Book;
use App\Contracts\Repositories\MediaRepository;

interface BookRepository extends AbstractRepository
{
    public function getDataInHomepage($with = [], $dataSelect = ['*']);

    public function getBooksByFields($with = [], $dataSelect = ['*'], $field, $attribute = []);

    public function getDataSearch(array $attribute, $with = [], $dataSelect = ['*']);

    public function booking(Book $book, array $data);

    public function review($bookId, array $data);

    public function getDataFilterInHomepage($with = [], $dataSelect = ['*'], $filters = []);

    public function show($id);

    public function store(array $attributes, MediaRepository $mediaRepository);

    public function update(array $attributes, Book $book, MediaRepository $mediaRepository);

    public function destroy(Book $book);

    public function getBookByCategory($categoryId, $dataSelect = ['*'], $with = []);

    public function getBookFilteredByCategory($categoryId, $attribute = [], $dataSelect = ['*'], $with = []);

    public function increaseView(Book $book);
}
