<?php

namespace App\Eloquent;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'code',
        'avatar',
        'position',
        'role',
        'office_id',
        'tags',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = bcrypt($value);
        }
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function books()
    {
        return $this->belongsToMany(Book::class)->withPivot('status', 'type');
    }

    public function reviews()
    {
        return $this->belongsToMany(Book::class, 'reviews')->withPivot('content', 'star');
    }

    public function suggestions()
    {
        return $this->hasMany(Suggestion::class);
    }

    public function owners()
    {
        return $this->belongsToMany(Book::class, 'owners', 'user_id');
    }

    public function usersFollowing()
    {
        return $this->hasMany(UserFollow::class, 'id', 'following_id');
    }

    public function usersFollower()
    {
        return $this->hasMany(UserFollow::class, 'id', 'follower_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Check book is ownered by current user
     *
     * @param integer $bookId
     * @return boolean
     */
    public function isOwnerBook($bookId)
    {
        return $this->owners()->where('book_id', $bookId)->count() !== 0;
    }
}
