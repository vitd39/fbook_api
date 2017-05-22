<?php

namespace App\Contracts\Repositories;

interface BookRepository extends AbstractRepository
{
    public function getDataInHomepage($with = [], $dataSelect = ['*']);
}
