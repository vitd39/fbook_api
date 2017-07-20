<?php

namespace App\Eloquent;

use Illuminate\Database\Eloquent\Model;

class UserFollow extends Model
{
    protected $table = 'user_follow';

    protected $fillable = [
        'following_id',
        'follower_id',
    ];

    public function userFlower()
    {
        return $this->belongsTo(User::class, 'folower_id');
    }

    public function userFlowing()
    {
        return $this->belongsTo(User::class, 'folowing_id');
    }
}
