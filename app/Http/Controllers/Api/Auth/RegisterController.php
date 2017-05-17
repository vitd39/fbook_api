<?php

namespace App\Http\Controllers\Api\Auth;

use App\Contracts\Repositories\UserRepository;
use App\Http\Controllers\Api\ApiController;
use App\Contracts\Services\PassportInterface;
use Illuminate\Database\QueryException;
use App\Exceptions\Api\NotQueryException;
use App\Exceptions\Api\NotFoundErrorException;

class RegisterController extends ApiController
{
    public function __construct(UserRepository $repository)
    {
        parent::__construct($repository);
    }
}
