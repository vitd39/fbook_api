<?php

namespace App\Repositories;

use App\Contracts\Repositories\MediaRepository;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Repositories\UploadableTrait;
use App\Eloquent\Media;

class MediaRepositoryEloquent extends AbstractRepositoryEloquent implements MediaRepository
{
    use UploadableTrait;

    public function model()
    {
        return new Media;
    }

    public function uploadAndSaveMedias(Model $relation, array $files, $path)
    {
        if (isset($files) && count($files)) {
            foreach ($files as $file) {
                $dataFile[] = [
                    'name' => $file['file']->getClientOriginalName(),
                    'size' => $file['file']->getSize(),
                    'type' => $file['type'],
                    'path' => $this->uploadFile($file['file'], $path, $file['type'] ? 'video' : 'image')
                ];
            }

            if (isset($dataFile)) {
                $relation->media()->createMany($dataFile);
            }
        }
    }
}
