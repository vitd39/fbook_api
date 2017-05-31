<?php

namespace App\Eloquent;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    const TYPE_IMAGE_BOOK = 1;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'path',
        'size',
        'type',
        'thumb_path',
        'target_type',
        'target_id',
    ];

    protected $hidden = ['target_id', 'target_type'];

    public function target()
    {
        return $this->morphTo();
    }

    private function responseMediaStorage($size = null)
    {
        return route('image',
            ['path' => app()['glide.builder']->getUrl($this->path, ['p' => ($size) ?: null])]
        );
    }

    public function getThumbPathAttribute()
    {
        if ($this->path) {
            return $this->responseMediaStorage('thumbnail');
        }

        return $this->thumb_path;
    }

    public function getFullPathAttribute()
    {
        return $this->responseMediaStorage('medium');
    }
}
