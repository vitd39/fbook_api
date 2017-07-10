<?php

namespace App\Contracts\Repositories;

interface UserRepository extends AbstractRepository
{
    public function getCurrentUser($userFromAuthServer);

    public function getDataBookOfUser($id, $action, $select = ['*'], $with = []);

    public function addTags(string $tags = null);

    public function getInterestedBooks($dataSelect = ['*'], $with = []);

    public function show($id);

    public function ownedBooks($dataSelect = ['*'], $with = []);
}
