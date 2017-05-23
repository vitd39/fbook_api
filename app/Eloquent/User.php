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
        return $this->belongsToMany(Book::class)->withPivot('content', 'star');
    }

    public function suggestions()
    {
        return $this->hasMany(Suggestion::class);
    }

    public function owners()
    {
        return $this->hasMany(Book::class, 'owner_id');
    }
}
