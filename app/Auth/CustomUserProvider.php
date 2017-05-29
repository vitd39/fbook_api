<?php

namespace App\Auth;

use App\Eloquent\User;
use Illuminate\Support\ServiceProvider;

class CustomUserProvider extends ServiceProvider
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function user()
    {
        return $this->user;
    }
}
