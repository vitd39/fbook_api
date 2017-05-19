<?php

namespace App\Eloquent;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
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
