<?php

namespace App\Contracts\Repositories;

use Illuminate\Database\Eloquent\Model;

interface MediaRepository extends AbstractRepository
{
    public function uploadAndSaveMedias(Model $relation, array $medias, $path);
}
