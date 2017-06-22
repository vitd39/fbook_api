<?php

namespace App\Contracts\Repositories;

interface UserRepository extends AbstractRepository
{
    public function getCurrentUser($userFromAuthServer);

    public function getDataBookByCurrentUser($action, $select = ['*'], $with = []);

    public function addTags(string $tags = null);
}
