<?php

namespace App\Eloquent\Counter;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table = 'counter_pages';

    protected $fillable = ['page'];

    public $timestamps = false;

    public function visitors()
    {
        return $this->belongsToMany(Visitor::class, 'counter_page_visitors', 'page_id', 'visitor_id');
    }
}
