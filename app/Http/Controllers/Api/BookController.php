<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Repositories\BookRepository;
use App\Contracts\Repositories\CategoryRepository;
use App\Exceptions\Api\NotFoundException;
use App\Http\Requests\Api\Book\BookFilteredByCategoryRequest;
use App\Http\Requests\Api\Book\BookFilterRequest;
use App\Http\Requests\Api\Book\FilterBookInCategoryRequest;
use App\Http\Requests\Api\Book\SearchRequest;
use App\Exceptions\Api\ActionException;
use App\Http\Requests\Api\Book\IndexRequest;
use App\Http\Requests\Api\Book\BookingRequest;
use App\Http\Requests\Api\Book\ReviewRequest;
use App\Http\Requests\Api\Book\StoreRequest;
use App\Contracts\Repositories\MediaRepository;
use App\Http\Requests\Api\Book\UpdateRequest;
use App\Http\Requests\Api\Book\UploadMediaRequest;

class BookController extends ApiController
{
    protected $select = [
        'id',
        'title',
        'description',
        'author',
        'publish_date',
        'total_page',
        'count_view',
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

    protected $counter;

    public function __construct(BookRepository $repository)
    {
        parent::__construct($repository);
    }

    public function index(IndexRequest $request)
    {
        $field = $request->input('field');

        if (!$field) {
            throw new ActionException;
        }

        $relations = ['image'];

        return $this->getData(function() use ($relations, $field) {
            $data = $this->repository->getBooksByFields($relations, $this->select, $field);

            $this->compacts['item'] = $this->reFormatPaginate($data);
        });
    }

    public function show($id)
    {
        $this->compacts['item'] = $this->repository->show($id);

        return $this->jsonRender();
    }

    public function store(StoreRequest $request, MediaRepository $mediaRepository)
    {
        $data = $request->all();

        return $this->doAction(function() use ($data, $mediaRepository) {
            $this->compacts['item'] = $this->repository->store($data, $mediaRepository);
        });
    }

    public function update(UpdateRequest $request, $id, MediaRepository $mediaRepository)
    {
        $data = $request->all();

        return $this->doAction(function() use ($data, $id, $mediaRepository) {
            $book = $this->repository->findOrFail($id);
            $this->before('update', $book);

            $this->compacts['item'] = $this->repository->update($data, $book, $mediaRepository);
        }, __FUNCTION__);
    }

    public function increaseView($id)
    {
        return $this->doAction(function() use ($id) {
            $book = $this->repository->findOrFail($id);

            $this->repository->increaseView($book);
        }, __FUNCTION__);
    }

    public function destroy($id)
    {
        return $this->doAction(function() use ($id) {
            $book = $this->repository->findOrFail($id);
            $this->before('delete', $book);

            $this->repository->destroy($book);
        }, __FUNCTION__);
    }

    public function search(SearchRequest $request)
    {
        $data = $request->all();

        return $this->getData(function() use($data) {
            $this->compacts['items'] = $this->reFormatPaginate(
                $this->repository->getDataSearch($data, ['image', 'category', 'office'], $this->select)
            );
        });
    }

    public function booking(BookingRequest $request)
    {
        $data = $request->all();

        return $this->doAction(function() use ($data) {
            $book = $this->repository->findOrfail($data['item']['book_id']);

            $this->repository->booking($book, $data);
        });
    }

    public function sortBy()
    {
        $this->compacts['items'] = config('model.condition_sort_book');

        return $this->jsonRender();
    }

    public function review(ReviewRequest $request, $bookId)
    {
        $data = $request->item;

        return $this->doAction(function() use ($bookId, $data) {
            $this->repository->review($bookId, $data);
        });
    }

    public function filter(BookFilterRequest $request)
    {
        $field = $request->input('field');

        $input = $request->all();

        $relations = [
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

        return $this->getData(function() use ($relations, $field, $input) {
            $data = $this->repository->getBooksByFields($relations, $this->select, $field, $input);

            $this->compacts['item'] = $this->reFormatPaginate($data);
        });
    }

    public function category($categoryId, CategoryRepository $categoryRepository)
    {
        $category = $categoryRepository->find($categoryId);

        if (!$category) {
            throw new NotFoundException;
        }

        $relations = [
            'image' => function ($q) {
                $q->select($this->imageSelect);
            },
            'office' => function ($q) {
                $q->select($this->officeSelect);
            }
        ];

        return $this->getData(function() use ($relations, $category) {
            $bookCategory = $this->repository->getBookByCategory($category->id, $this->select, $relations);
            $currentPage = $bookCategory->currentPage();

            $this->compacts['item'] = [
                'total' => $bookCategory->total(),
                'per_page' => $bookCategory->perPage(),
                'current_page' => $currentPage,
                'next_page' => ($bookCategory->lastPage() > $currentPage) ? $currentPage + 1 : null,
                'prev_page' => $currentPage - 1 ?: null,
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'data' => $bookCategory->items(),
                ]
            ];
        });
    }

    public function filterCategory($categoryId, BookFilteredByCategoryRequest $request, CategoryRepository $categoryRepository)
    {
        $category = $categoryRepository->find($categoryId);

        $input = $request->all();

        if (!$category) {
            throw new NotFoundException;
        }

        $relations = [
            'image' => function ($q) {
                $q->select($this->imageSelect);
            },
            'office' => function ($q) {
                $q->select($this->officeSelect);
            }
        ];

        return $this->getData(function() use ($relations, $category, $input) {
            $bookCategory = $this->repository->getBookFilteredByCategory($category->id, $input, $this->select, $relations);
            $currentPage = $bookCategory->currentPage();

            $this->compacts['item'] = [
                'total' => $bookCategory->total(),
                'per_page' => $bookCategory->perPage(),
                'current_page' => $currentPage,
                'next_page' => ($bookCategory->lastPage() > $currentPage) ? $currentPage + 1 : null,
                'prev_page' => $currentPage - 1 ?: null,
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'data' => $bookCategory->items(),
                ]
            ];
        });
    }

    public function uploadMedia(UploadMediaRequest $request, MediaRepository $mediaRepository)
    {
        $data = $request->all();

        return $this->doAction(function() use ($data, $mediaRepository) {
            $book = $this->repository->findOrFail($data['book_id']);
            $this->before('update', $book);

            $this->compacts['item'] = $this->repository->uploadMedia($book, $data, $mediaRepository);
        }, __FUNCTION__);
    }
}
