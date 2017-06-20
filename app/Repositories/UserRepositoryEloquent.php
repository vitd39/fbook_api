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
                'avatar' => $userFromAuthServer['avatar'],
            ])->fresh();
        }

        return $currentUser;
    }

    public function getDataBookByCurrentUser($action, $select = ['*'], $with = [])
    {
        if (in_array($action, array_keys(config('model.book_user.status')))) {
            return $this->getDataBookOfUser(config('model.book_user.status.' . $action), $select, $with);
        }

        if ($action == config('model.user_sharing_book')) {
            return $this->user->owners()
                ->select($select)
                ->with($with)
                ->paginate(config('paginate.default'));
        }
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

    public function addTags(string $tags)
    {
        $this->user->update([
            'tags' => $tags,
        ]);
    }
}
