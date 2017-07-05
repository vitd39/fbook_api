<?php

namespace App\Eloquent\Counter;

use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    protected $table = 'counter_visitors';

    protected $fillable = ['visitor'];

    public $timestamps = false;

    public function pages()
    {
        return $this->belongsToMany(Page::class, 'counter_page_visitors', 'visitor_id', 'page_id');
    }
}
