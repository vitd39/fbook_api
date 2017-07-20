<?php

namespace App\Contracts\Repositories;

interface UserRepository extends AbstractRepository
{
    public function getCurrentUser($userFromAuthServer);

    public function getDataBookOfUser($id, $action, $select = ['*'], $with = [], $officeId = '');

    public function addTags(string $tags = null);

    public function getInterestedBooks($dataSelect = ['*'], $with = [], $officeId = '');

    public function show($id);

    public function ownedBooks($dataSelect = ['*'], $with = []);

    public function getListWaitingApprove($dataSelect = ['*'], $with = [], $officeId = '');

    public function getBookApproveDetail($bookId, $dataSelect = ['*'], $with = []);
}
