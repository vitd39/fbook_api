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

    public function target()
    {
        return $this->morphTo();
    }
}
