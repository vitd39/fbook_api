<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepository;

class UserRepositoryEloquent extends AbstractRepositoryEloquent implements UserRepository
{
    public function model()
    {
        return new \App\Eloquent\User;
    }

    public function getCurrentUser($userFromAuthServer)
    {
        $userInDatabase = $this->model()->whereEmail($userFromAuthServer['email'])->first();
        $currentUser = $userInDatabase;
        
        if (!count($userInDatabase)) {
            $currentUser = $this->model()->create([
                'name' => $userFromAuthServer['name'],
                'email' => $userFromAuthServer['email'],
            ]);
        }

        return $currentUser;
    }

    public function getReadingBooksByCurrentUser($select = ['*'], $with = [])
    {
        return $this->getDataBookOfUser(config('model.book_user.status.reading'), $select = ['*'], $with = []);
    }

    protected function getDataBookOfUser($status, $select = ['*'], $with = [])
    {
        if (in_array($status, array_values(config('model.book_user.status')))) {
            return $this->user->books()
                ->select($select)
                ->with($with)
                ->wherePivot('status', $status)
                ->paginate(config('paginate.default'));
        }
    }
}
