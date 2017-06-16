<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Repositories\UserRepository;
use App\Exceptions\Api\ActionException;

class UserController extends ApiController
{
    protected $bookSelect = [
        'id',
        'title',
        'description',
        'author',
        'publish_date',
        'total_page',
        'avg_star',
        'count_view',
        'status',
        'category_id',
        'office_id'
    ];

    protected $imageSelect = [
        'path',
        'size',
        'thumb_path',
        'target_id',
        'target_type',
    ];

    protected $categorySelect = [
        'id',
        'name',
    ];

    protected $officeSelect = [
        'id',
        'name',
    ];

    protected $relations = [];

    public function __construct(UserRepository $repository)
    {
        parent::__construct($repository);

        $this->relations = [
            'image' => function ($q) {
                $q->select($this->imageSelect);
            },
            'category' => function ($q) {
                $q->select($this->categorySelect);
            },
            'office' => function ($q) {
                $q->select($this->officeSelect);
            }
        ];
    }

    public function getBook($action)
    {
        if (
            !in_array($action, array_keys(config('model.book_user.status')))
            && $action != config('model.user_sharing_book')
        ) {
            throw new ActionException;
        }

        return $this->getData(function() use ($action) {
            $data = $this->repository->getDataBookByCurrentUser($action, $this->bookSelect, $this->relations);

            $this->compacts['items'] = $this->reFormatPaginate($data);
        });
    }

    public function getUserFromToken()
    {
        return $this->requestAction(function() {
            $this->compacts['item'] = $this->user;
        });
    }
}
