<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Repositories\UserRepository;
use App\Exceptions\Api\ActionException;
use App\Http\Requests\Api\User\AddTagsRequest;

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
        'books.status',
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

    public function show($id)
    {
        return $this->requestAction(function() use ($id) {
            $this->compacts['item'] = $this->repository->show($id);
        });
    }

    public function getBook($id, $action)
    {
        if (
            !in_array($action, array_keys(config('model.book_user.status')))
            && $action != config('model.user_sharing_book')
        ) {
            throw new ActionException;
        }

        return $this->getData(function() use ($id, $action) {
            $data = $this->repository->getDataBookOfUser($id, $action, $this->bookSelect, $this->relations);

            $this->compacts['items'] = $this->reFormatPaginate($data);
        });
    }

    public function getUserFromToken()
    {
        return $this->requestAction(function() {
            $this->compacts['item'] = $this->user;
        });
    }

    public function addTags(AddTagsRequest $request)
    {
        $data = $request->item;

        return $this->requestAction(function() use ($data) {
            $this->repository->addTags($data['tags']);
        });
    }

    public function getInterestedBooks()
    {
        return $this->requestAction(function() {
            $this->compacts['items'] = $this->reFormatPaginate(
                $this->repository->getInterestedBooks($this->bookSelect, ['image'])
            );
        });
    }

    public function ownedBooks()
    {
        return $this->requestAction(function() {
            $this->compacts['items'] = $this->reFormatPaginate(
                $this->repository->ownedBooks($this->bookSelect, ['image'])
            );
        });
    }
}
