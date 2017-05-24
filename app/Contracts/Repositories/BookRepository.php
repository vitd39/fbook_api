<?php

namespace App\Contracts\Repositories;

interface BookRepository extends AbstractRepository
{
    public function getDataInHomepage($with = [], $dataSelect = ['*']);

    public function getBooksByFields($with = [], $dataSelect = ['*'], $field);
}
