<?php

namespace App\Eloquent;

use Illuminate\Support\Facades\Event;

class Book extends AbstractEloquent
{
    const STATUS = [
        'waiting' => 1,
        'reading' => 2,
        'done' => 3,
    ];

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

    protected $hidden = ['owner_id', 'category_id', 'office_id'];

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

    public function userReadingBook()
    {
        return $this->belongsToMany(User::class)->wherePivot('status', self::STATUS['reading']);
    }

    public function usersWaitingBook()
    {
        return $this->belongsToMany(User::class)->wherePivot('status', self::STATUS['waiting']);
    }

    public function reviews()
    {
        return $this->belongsToMany(User::class, 'reviews')->withPivot('content', 'star');
    }

    public function reviewsDetailBook()
    {
        return $this->hasMany(Review::class, 'book_id')->with('user');
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'target');
    }

    public function image()
    {
        return $this->morphOne(Media::class, 'target')->where('type', Media::TYPE_IMAGE_BOOK);
    }

    public function scopeGetData($query, $field, $filters = [], $orderBy = 'DESC')
    {
        return $query->where(function ($query) use ($field, $filters) {
            if ($field == config('model.filter_books.view.field')) {
                $query->where(config('model.filter_books.view.field'), '>', 0);
            }

            if ($field == config('model.filter_books.rating.field')) {
                $query->where(config('model.filter_books.rating.field'), '>', 0);
            }

            if ($filters) {
                foreach ($filters as $value) {
                    foreach ($value as $filter => $filterIds) {
                        if (in_array($filter, config('model.filter_type'))) {
                            $query->whereIn($filter . '_id', $filterIds);
                        }
                    }
                }
            }
        })->orderBy($field, $orderBy);
    }

    public function getAvgStarAttribute($value)
    {
        return round($value, config('settings.round_average_star'));
    }

    public static function boot()
    {
        parent::boot();

        static::deleted(function ($book) {
            Event::fire('book.deleted', $book);
        });
    }
}
