<?php

namespace App\Repositories;

use App\Contracts\Repositories\UserRepository;
use App\Eloquent\Book;

class UserRepositoryEloquent extends AbstractRepositoryEloquent implements UserRepository
{
    protected $userSelect = [
        'id',
        'name',
        'email',
        'phone',
        'code',
        'position',
        'role',
        'office_id',
        'avatar',
        'tags',
    ];

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

    public function getDataBookOfUser($id, $action, $select = ['*'], $with = [], $officeId = '')
    {
        if (
            in_array($action, array_keys(config('model.book_user.status')))
            && in_array(config('model.book_user.status.' . $action), array_values(config('model.book_user.status')))
        ) {
            return $this->model()->findOrFail($id)->books()
                ->getBookByOffice($officeId)
                ->with($with)
                ->wherePivot('status', config('model.book_user.status.' . $action))
                ->paginate(config('paginate.default'), $select);
        }

        if ($action == config('model.user_sharing_book')) {
            return $this->model()->findOrFail($id)->owners()
                ->getBookByOffice($officeId)
                ->with(array_merge($with, [
                        'usersReading' => function($query) {
                            $query->select(array_merge($this->userSelect, ['owner_id']));
                            $query->orderBy('book_user.created_at', 'ASC')->limit(1);
                        }
                    ])
                )
                ->paginate(config('paginate.default'), $select);
        }
    }

    public function addTags(string $tags = null)
    {
        $this->user->update([
            'tags' => $tags,
        ]);
    }

    public function getInterestedBooks($dataSelect = ['*'], $with = [], $officeId = '')
    {
        if ($this->user->tags) {
            $tags = explode(',', $this->user->tags);

            return app(Book::class)
                ->getLatestBooks($dataSelect, $with)
                ->getBookByOffice($officeId)
                ->whereIn('category_id', $tags)
                ->paginate(config('paginate.default'));
        }

        return app(Book::class)
            ->getLatestBooks($dataSelect, $with)
            ->getBookByOffice($officeId)
            ->paginate(config('paginate.default'));
    }

    public function show($id)
    {
        return $this->model()->findOrFail($id);
    }

    public function ownedBooks($dataSelect = ['*'], $with = [])
    {
        $books = app(Book::class)
            ->select($dataSelect)
            ->with(array_merge($with, ['userReadingBook' => function($query) {
                $query->select('id', 'name', 'avatar', 'position');
            }]))
            ->where('owner_id', $this->user->id)
            ->paginate(config('paginate.default'));

        foreach ($books->items() as $book) {
            $book->user_reading_book = $book->userReadingBook->first();
            unset($book['userReadingBook']);
        }

        return $books;
    }

    public function getListWaitingApprove($dataSelect = ['*'], $with = [], $officeId = '')
    {
        $books = $this->user->owners()
            ->select($dataSelect)
            ->with(array_merge($with, [
                'usersWaiting' => function($query) {
                    $query->select('id', 'name', 'avatar', 'position');
                    $query->orderBy('book_user.created_at', 'ASC');
                },
                'usersReturning' => function($query) {
                    $query->select('id', 'name', 'avatar', 'position');
                    $query->orderBy('book_user.created_at', 'ASC')->limit(1);
                }
            ]))
            ->getBookByOffice($officeId)
            ->orderBy('created_at', 'DESC')
            ->paginate(config('paginate.default'));

        return $books;
    }

    public function getBookApproveDetail($bookId, $dataSelect = ['*'], $with = [])
    {
        return $this->user->owners()->where('book_id', $bookId)
            ->select($dataSelect)
            ->with(array_merge($with, [
                'usersWaiting' => function($query) {
                    $query->select('id', 'name', 'avatar', 'position', 'email');
                    $query->orderBy('book_user.created_at', 'ASC');
                },
                'usersReturning' => function($query) {
                    $query->select('id', 'name', 'avatar', 'position', 'email');
                    $query->orderBy('book_user.created_at', 'ASC')->limit(1);
                },
                'usersReading' => function($query) {
                    $query->select('id', 'name', 'avatar', 'position', 'email');
                    $query->orderBy('book_user.created_at', 'ASC')->limit(1);
                },
                'usersReturned' => function($query) {
                    $query->select('id', 'name', 'avatar', 'position', 'email');
                    $query->orderBy('book_user.created_at', 'ASC');
                }
            ]))
            ->firstOrFail();
    }
}
