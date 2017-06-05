<?php

namespace App\Policies;

use App\Eloquent\User;
use App\Eloquent\Book;

class BookPolicy extends AbstractPolicy
{
    public function read(User $user, Book $ability)
    {
        return true;
    }
}
