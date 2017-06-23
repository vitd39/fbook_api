<?php

namespace App\Repositories;

use App\Contracts\Repositories\BookRepository;
use App\Eloquent\Book;
use App\Eloquent\BookUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Exceptions\Api\NotFoundException;
use App\Exceptions\Api\UnknownException;
use Log;
use App\Contracts\Repositories\MediaRepository;
use App\Exceptions\Api\ActionException;
use App\Traits\Repositories\UploadableTrait;

class BookRepositoryEloquent extends AbstractRepositoryEloquent implements BookRepository
{
    use UploadableTrait;

    public function model()
    {
        return new \App\Eloquent\Book;
    }

    public function getDataInHomepage($with = [], $dataSelect = ['*'])
    {
        $limit = config('paginate.book_home_limit');

        return [
            [
                'key' => config('model.filter_books.latest.key'),
                'title' => config('model.filter_books.latest.title'),
                'data' => $this->getLatestBooks($with, $dataSelect, $limit)->items(),
            ],
            [
                'key' => config('model.filter_books.view.key'),
                'title' => config('model.filter_books.view.title'),
                'data' => $this->getBooksByCountView($with, $dataSelect, $limit)->items(),
            ],
            [
                'key' => config('model.filter_books.rating.key'),
                'title' => config('model.filter_books.rating.title'),
                'data' => $this->getBooksByRating($with, $dataSelect, $limit)->items(),
            ],
            [
                'key' => config('model.filter_books.waiting.key'),
                'title' => config('model.filter_books.waiting.title'),
                'data' => $this->getBooksByWaiting($with, $dataSelect, $limit)->items(),
            ],
        ];
    }

    public function getDataFilterInHomepage($with = [], $dataSelect = ['*'], $attribute = [])
    {
        $limit = config('paginate.book_home_limit');

        return [
            [
                'key' => config('model.filter_books.latest.key'),
                'title' => config('model.filter_books.latest.title'),
                'data' => $this->getLatestBooks($with, $dataSelect, $limit, $attribute)->items(),
            ],
            [
                'key' => config('model.filter_books.view.key'),
                'title' => config('model.filter_books.view.title'),
                'data' => $this->getBooksByCountView($with, $dataSelect, $limit, $attribute)->items(),
            ],
            [
                'key' => config('model.filter_books.rating.key'),
                'title' => config('model.filter_books.rating.title'),
                'data' => $this->getBooksByRating($with, $dataSelect, $limit, $attribute)->items(),
            ],
            [
                'key' => config('model.filter_books.waiting.key'),
                'title' => config('model.filter_books.waiting.title'),
                'data' => $this->getBooksByWaiting($with, $dataSelect, $limit, $attribute)->items(),
            ],
        ];
    }

    public function getDataSearch(array $attribute, $with = [], $dataSelect = ['*'])
    {
        $input = $this->getDataInput($attribute);

        return $this->model()
            ->select($dataSelect)
            ->with($with)
            ->where(function ($query) use ($attribute) {
                if (isset($attribute['conditions']) && $attribute['conditions']) {
                    foreach ($attribute['conditions'] as $conditions) {
                        foreach ($conditions as $type => $typeIds) {
                            if (in_array($type, config('model.filter_type')) && count($typeIds)) {
                                $query->whereIn($type . '_id', $typeIds);
                            }
                        }
                    }
                }
                if (isset($attribute['search']['keyword']) && $attribute['search']['keyword']) {
                    $query->where(function ($query) use($attribute) {
                        if (isset($attribute['search']['field']) && $attribute['search']['field']) {
                            $query->where($attribute['search']['field'], 'LIKE', '%' . $attribute['search']['keyword'] . '%');
                        } else {
                            foreach (config('model.book.fields') as $field) {
                                $query->where($field, 'LIKE', '%' . $attribute['search']['keyword'] . '%');
                            }
                        }
                    });
                }
            })
            ->orderBy($input['sort']['field'], $input['sort']['type'])
            ->paginate(config('paginate.default'));
    }

    protected function getLatestBooks($with = [], $dataSelect = ['*'], $limit = '', $attribute = [])
    {
        $input = $this->getDataInput($attribute);

        return $this->model()
            ->select($dataSelect)
            ->with($with)
            ->getData(config('model.filter_books.latest.field'), $input['filters'])
            ->orderBy($input['sort']['field'], $input['sort']['type'])
            ->paginate($limit ?: config('paginate.default'));
    }

    protected function getBooksByCountView($with = [], $dataSelect = ['*'], $limit = '', $attribute = [])
    {
        $input = $this->getDataInput($attribute);

        return $this->model()
            ->select($dataSelect)
            ->with($with)
            ->getData(config('model.filter_books.view.field'), $input['filters'])
            ->orderBy($input['sort']['field'], $input['sort']['type'])
            ->paginate($limit ?: config('paginate.default'));
    }

    protected function getBooksByRating($with = [], $dataSelect = ['*'], $limit = '', $attribute = [])
    {
        $input = $this->getDataInput($attribute);

        return $this->model()
            ->select($dataSelect)
            ->with($with)
            ->getData(config('model.filter_books.view.field'), $input['filters'])
            ->orderBy($input['sort']['field'], $input['sort']['type'])
            ->paginate($limit ?: config('paginate.default'));
    }

    protected function getBooksByWaiting($with = [], $dataSelect = ['*'], $limit = '', $attribute = [])
    {
        $input = $this->getDataInput($attribute);

        $numberOfUserWaitingBook = \DB::table('books')
            ->join('book_user', 'books.id', '=', 'book_user.book_id')
            ->select('book_user.book_id', \DB::raw('count(book_user.user_id) as count_waiting'))
            ->where('book_user.status', config('model.book_user.status.waiting'))
            ->groupBy('book_user.book_id')
            ->orderBy('count_waiting', 'DESC')
            ->limit($limit ?: config('paginate.default'))
            ->get();

        $books = $this->model()
            ->select($dataSelect)
            ->with($with)
            ->whereIn('id', $numberOfUserWaitingBook->pluck('book_id')->toArray())
            ->getData($input['sort']['field'], $input['filters'], $input['sort']['type'])
            ->paginate($limit ?: config('paginate.default'));

        foreach ($books->items() as $book) {
            $book->count_waiting = $numberOfUserWaitingBook->where('book_id', $book->id)->first()->count_waiting;
        }

        return $books;
    }

    public function getBooksByFields($with = [], $dataSelect = ['*'], $field, $attribute = [])
    {
        switch ($field) {
            case config('model.filter_books.view.key'):
                return $this->getBooksByCountView($with, $dataSelect,'', $attribute);

            case config('model.filter_books.latest.key'):
                return $this->getLatestBooks($with, $dataSelect, '', $attribute);

            case config('model.filter_books.rating.key'):
                return $this->getBooksByRating($with, $dataSelect, '', $attribute);

            case config('model.filter_books.waiting.key'):
                return $this->getBooksByWaiting($with, $dataSelect, '', $attribute);
        }
    }

    public function booking(Book $book, array $attributes)
    {
        if ($book->status == config('model.book.status.available')) {
            $waitingList = $book->usersWaitingBook()->orderBy('created_at')->get();

            if (count($waitingList)) {
                if ($waitingList->first()->pivot->user_id == $this->user->id) {

                    $book->update(['status' => config('model.book.status.unavailable')]);

                    $book->users()->updateExistingPivot($this->user->id, [
                        'book_user.status' => config('model.book_user.status.reading'),
                    ]);
                } else {
                    $checkUser = $book->users()->find($this->user->id);

                    if ($checkUser) {
                        throw new ActionException('not_first_waiting_list');
                    }

                    $book->users()->attach($this->user->id, [
                        'status' => config('model.book_user.status.waiting'),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }
            } else {
                $book->update(['status' => config('model.book.status.unavailable')]);

                $book->users()->attach($this->user->id, [
                    'book_user.status' => config('model.book_user.status.reading'),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        } else {
            $checkUser = $book->users()->find($this->user->id);

            if ($checkUser) {
                if (
                    $checkUser->pivot->status == config('model.book_user.status.reading')
                    && $attributes['item']['status'] == config('model.book_user.status.done')
                ) {
                    $book->update(['status' => config('model.book.status.available')]);

                    $book->users()->detach($this->user->id);
                } elseif (
                    $checkUser->pivot->status == config('model.book_user.status.waiting')
                    && $attributes['item']['status'] == config('model.book_user_status_cancel')
                ) {
                    $book->users()->detach($this->user->id);
                } else {
                    $book->users()->updateExistingPivot($this->user->id, [
                        'status' => config('model.book_user.status.waiting'),
                    ]);
                }
            } else {
                $book->users()->attach($this->user->id, [
                    'status' => config('model.book_user.status.waiting'),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }

    public function review($bookId, array $data)
    {
        $book = $this->model()->findOrFail($bookId);
        $dataReview = array_only($data, ['content', 'star']);
        $dataReview['created_at'] = $dataReview['updated_at'] = Carbon::now();

        $book->reviews()->detach($this->user->id);

        $book->reviews()->attach([
            $this->user->id => $dataReview
        ]);

        if (isset($dataReview['star'])) {
            Event::fire('books.averageStar', [
                [
                    'book' => $book,
                    'star' => $dataReview['star'],
                ]
            ]);
        }
    }

    protected function getDataInput($attribute = [])
    {
        $sort = [
            'field' => 'created_at',
            'type' => 'desc'
        ];
        $filters = [];

        if (isset($attribute['sort']['field']) && $attribute['sort']['field']) {
            $sort['field'] = $attribute['sort']['field'];
        }

        if (isset($attribute['sort']['order_by']) && $attribute['sort']['order_by']) {
            $sort['type'] = $attribute['sort']['order_by'];
        }

        if (isset($attribute['filters']) && $attribute['filters']) {
            $filters = $attribute['filters'];
        }

        return compact('sort', 'filters');
    }

    public function show($id)
    {
        try {
            $book = $this->model()->findOrFail($id);
            $book->user_reading_book = $book->userReadingBook()->select('id', 'name', 'avatar', 'position')->first();

            return  $book->load(['media', 'reviewsDetailBook',
                'usersWaitingBook' => function($query) {
                    $query->select('id', 'name', 'avatar', 'position');
                    $query->orderBy('book_user.created_at', 'ASC');
                },
                'category' => function($query) {
                    $query->select('id', 'name');
                },
                'office' => function($query) {
                    $query->select('id', 'name');
                },
                'owner' => function($query) {
                    $query->select('id', 'name');
                },
            ]);
        } catch (ModelNotFoundException $e) {
            Log::error($e->getMessage());

            throw new NotFoundException();
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            throw new UnknownException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Upload and save medias when user add new book
     *
     * @param array $medias
     * @param Book $book
     * @param MediaRepository $mediaRepository
     */
    protected function uploadAndSaveMediasForBook(array $medias, Book $book, MediaRepository $mediaRepository)
    {
        $dataMedias = [];

        foreach ($medias as $media) {
            $dataMedias[] = array_only($media, ['file', 'type']);
        }

        $mediaRepository->uploadAndSaveMedias(
            $book,
            $dataMedias,
            strtolower(class_basename($this->model()))
        );
    }

    /**
     * Get book info by code
     *
     * @param string $code
     * @return mixed
     */
    protected function getBookByCode(string $code)
    {
        return $this->model()->whereCode($code)->first();
    }

    public function store(array $attributes, MediaRepository $mediaRepository)
    {
        $dataBook = array_only($attributes, $this->model()->getFillable());
        $dataBook['owner_id'] = $this->user->id;
        $book = $this->model()->create($dataBook);

        if (isset($attributes['medias'])) {
            $this->uploadAndSaveMediasForBook($attributes['medias'], $book, $mediaRepository);
        }
        
        return $book->load('category', 'office', 'media');
    }

    public function update(array $attributes, Book $book, MediaRepository $mediaRepository)
    {
        $dataBook = array_only($attributes, $this->model()->getFillable());
        $bookWithCurrentCode = $this->getBookByCode($attributes['code']);

        if ($bookWithCurrentCode && $bookWithCurrentCode->id != $book->id) {
            throw new ActionException(__FUNCTION__);
        }

        $book->update($dataBook);

        if (isset($attributes['medias'])) {
            foreach ($book->media as $media) {
                $this->destroyFile($media->path);
            }

            $book->media()->delete();
            $this->uploadAndSaveMediasForBook($attributes['medias'], $book, $mediaRepository);
        }
        
        return $book->load('category', 'office', 'media');
    }


    public function destroy(Book $book)
    {
        $book->delete();
    }

    public function getBookByCategory($categoryId, $dataSelect = ['*'], $with = [])
    {
        return $this->select($dataSelect)->with($with)->where('category_id', $categoryId)->paginate(config('paginate.default'));
    }
}
