<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Repositories\UserRepository;

class UserController extends ApiController
{
    public function __construct(UserRepository $repository)
    {
        parent::__construct($repository);
    }
}
