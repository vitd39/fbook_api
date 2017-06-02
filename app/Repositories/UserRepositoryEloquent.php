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
}
