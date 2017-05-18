<?php

namespace App\Eloquent;

class Book extends AbstractEloquent
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'author',
        'publish_date',
        'total_page',
        'avg_star',
        'code',
        'count_view',
        'status',
        'owner_id',
        'category_id',
        'office_id',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('status', 'type');
    }

    public function reviews()
    {
        return $this->belongsToMany(User::class, 'reviews')->withPivot('content', 'star');
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'target');
    }
}
