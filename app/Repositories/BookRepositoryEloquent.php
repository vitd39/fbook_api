<?php

namespace App\Repositories;

use App\Contracts\Repositories\BookRepository;
use App\Eloquent\Book;

class BookRepositoryEloquent extends AbstractRepositoryEloquent implements BookRepository
{
    public function model()
    {
        return new \App\Eloquent\Book;
    }

    public function getDataInHomepage($with = [], $dataSelect = ['*'])
    {
        $limit = config('paginate.book_home_limit');

        return [
            [
                'key' => 'latest',
                'title' => translate('title_key.latest'),
                'data' => $this->getLatestBooks($with, $dataSelect, $limit)->items(),
            ],
            [
                'key' => 'view',
                'title' => translate('title_key.view'),
                'data' => $this->getBooksByCountView($with, $dataSelect, $limit)->items(),
            ],
            [
                'key' => 'rating',
                'title' => translate('title_key.rating'),
                'data' => $this->getBooksByRating($with, $dataSelect, $limit)->items(),
            ],
            [
                'key' => 'waiting',
                'title' => translate('title_key.waiting'),
                'data' => $this->getBooksByWaiting($with, $dataSelect, $limit)->items(),
            ],
        ];
    }

    protected function getLatestBooks($with = [], $dataSelect = ['*'], $limit = '')
    {
        return $this->model()
            ->select($dataSelect)
            ->with($with)
            ->getData('created_at')
            ->paginate($limit ?: config('paginate.default'));
    }

    protected function getBooksByCountView($with = [], $dataSelect = ['*'], $limit = '')
    {
        return $this->model()
            ->select($dataSelect)
            ->with($with)
            ->getData('count_view')
            ->paginate($limit ?: config('paginate.default'));
    }

    protected function getBooksByRating($with = [], $dataSelect = ['*'], $limit = '')
    {
        return $this->model()
            ->select($dataSelect)
            ->with($with)
            ->getData('avg_star')
            ->paginate($limit ?: config('paginate.default'));
    }

    protected function getBooksByWaiting($with = [], $dataSelect = ['*'], $limit = '')
    {
        $numberOfUserWaitingBook = \DB::table('books')
            ->join('book_user', 'books.id', '=', 'book_user.book_id')
            ->select('book_user.book_id', \DB::raw('count(book_user.user_id) as count_waiting'))
            ->where('book_user.status', Book::STATUS['waiting'])
            ->groupBy('book_user.book_id')
            ->orderBy('count_waiting', 'DESC')
            ->limit($limit ?: config('paginate.default'))
            ->get();

        $books = $this->model()
            ->select($dataSelect)
            ->with($with)
            ->whereIn('id', $numberOfUserWaitingBook->pluck('book_id')->toArray())
            ->paginate($limit ?: config('paginate.default'));

        foreach ($books->items() as $book) {
            $book->count_waiting = $numberOfUserWaitingBook->where('book_id', $book->id)->first()->count_waiting;
        }

        return $books;
    }
}
