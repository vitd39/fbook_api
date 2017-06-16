<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Eloquent\Book;
use Faker\Factory;
use App\Eloquent\Category;
use App\Eloquent\Office;
use Illuminate\Http\UploadedFile;

class BookTest extends TestCase
{
    use DatabaseTransactions;

    public function dataFilterBook()
    {
        return [
            'filters' => [
                ['category' => [2, 3]],
                ['office' => [4, 5]],
            ],
            'sort' => [
                'key' => 'title',
                'order_by' => 'desc'
            ],
        ];
    }

    /* TEST GET BOOKS */

    public function testGetBooksByRatingSuccess()
    {
        $response = $this->call('GET', route('api.v0.books.index', ['field' => 'rating']), [], [], [], $this->getHeaders());

        $response->assertJsonStructure([
            'item' => [
                'total', 'per_page', 'current_page', 'next_page', 'prev_page', 'data'
            ],
            'message' => [
                'status', 'code',
            ],
        ])->assertJson([
            'message' => [
                'status' => true,
                'code' => 200,
            ]
        ])->assertStatus(200);
    }

    public function testGetBooksByLatestSuccess()
    {
        $response = $this->call('GET', route('api.v0.books.index', ['field' => 'latest']), [], [], [], $this->getHeaders());

        $response->assertJsonStructure([
            'item' => [
                'total', 'per_page', 'current_page', 'next_page', 'prev_page', 'data'
            ],
            'message' => [
                'status', 'code',
            ],
        ])->assertJson([
            'message' => [
                'status' => true,
                'code' => 200,
            ]
        ])->assertStatus(200);
    }

    public function testGetBooksByViewSuccess()
    {
        $response = $this->call('GET', route('api.v0.books.index', ['field' => 'view']), [], [], [], $this->getHeaders());

        $response->assertJsonStructure([
            'item' => [
                'total', 'per_page', 'current_page', 'next_page', 'prev_page', 'data'
            ],
            'message' => [
                'status', 'code',
            ],
        ])->assertJson([
            'message' => [
                'status' => true,
                'code' => 200,
            ]
        ])->assertStatus(200);
    }

    public function testGetBooksInvalid()
    {
        $response = $this->call('GET', route('api.v0.books.index', ['field' => 'viewa']), [], [], [], $this->getHeaders());
        $response->assertJsonStructure([
            'message' => [
                'status', 'code', 'description',
            ],
        ])->assertJson([
            'message' => [
                'status' => false,
                'code' => 422,
            ]
        ])->assertStatus(422);
    }

    /* TEST SHOW DETAIL BOOK */

    public function testShowBookWithBookInvalid()
    {
        $headers = $this->getHeaders();
        $response = $this->call('GET', route('api.v0.books.show', 'xxx'), [], [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code', 'description'
            ],
        ])->assertJson([
            'message' => [
                'status' => false,
                'code' => 404,
            ]
        ])->assertStatus(404);
    }

    public function testShowBookWithBookNotFound()
    {
        $headers = $this->getHeaders();
        $response = $this->call('GET', route('api.v0.books.show', 0), [], [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code', 'description'
            ],
        ])->assertJson([
            'message' => [
                'status' => false,
                'code' => 404,
            ]
        ])->assertStatus(404);
    }

    public function testShowBooksSuccess()
    {
        $headers = $this->getHeaders();
        $book = Book::first();

        $response = $this->call('GET', route('api.v0.books.show', $book->id), [], [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code',
            ],
        ])->assertJson([
            'message' => [
                'status' => true,
                'code' => 200,
            ]
        ])->assertStatus(200);
    }

    /* TEST LIST BOOKS */

    public function testListBookSearchSuccess()
    {
        $headers = $this->getHeaders();
        $data = [
            'search' => [
                'field' => 'title',
                'keyword' => 'a',
            ],
            'conditions' => [
                [
                    'category' => [
                        1, 2, 3
                    ]
                ],
                [
                    'office' => [
                        1, 2, 3
                    ]
                ],
            ],
            'sort' => [
                'field' => 'avg_star',
                'order_by' => 'desc',
            ],
        ];

        $response = $this->call('POST', 'api/v0/search', $data, [], [], $headers);
        $response->assertJsonStructure([
            'items' => [
                'total', 'per_page', 'current_page', 'next_page', 'prev_page', 'data'
            ],
            'message' => [
                'status', 'code',
            ],
        ])->assertJson([
            'message' => [
                'status' => true,
                'code' => 200,
            ]
        ])->assertStatus(200);
    }

    public function testListBookSearchWithNotInput()
    {
        $headers = $this->getHeaders();

        $response = $this->call('POST', 'api/v0/search', [], [], [], $headers);
        $response->assertJsonStructure([
            'items' => [
                'total', 'per_page', 'current_page', 'next_page', 'prev_page', 'data'
            ],
            'message' => [
                'status', 'code',
            ],
        ])->assertJson([
            'message' => [
                'status' => true,
                'code' => 200,
            ]
        ])->assertStatus(200);
    }

    public function testListBookSearchWithFieldInValid()
    {
        $headers = $this->getHeaders();
        $data = [
            'search' => [
                'field' => 'a',
                'keyword' => 'a',
            ],
            'conditions' => [
                [
                    'category' => [
                        1, 2, 3
                    ]
                ],
                [
                    'office' => [
                        1, 2, 3
                    ]
                ],
            ],
            'sort' => [
                'field' => 'a',
                'order_by' => 'a',
            ],
        ];

        $response = $this->call('POST', 'api/v0/search', $data, [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code', 'description'
            ],
        ])->assertJson([
            'message' => [
                'status' => false,
                'code' => 422,
            ]
        ])->assertStatus(422);
    }

    /* TEST BOOKING BOOK */
    public function testBookingStatusDoneSuccess()
    {
        $headers = $this->getFauthHeaders();
        $book = Book::first();
        $user = $book->userReadingBook()->first();

        $newUpdate['book_id'] = $book->id;
        $newUpdate['status'] = config('model.book_user.status.done');
        $newUpdate['user_id'] = $user ? $user->id : $this->createUser()->id;

        $response = $this->call('POST', route('api.v0.books.booking', $book->id), ['item' => $newUpdate], [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code',
            ],
        ])->assertJson([
            'message' => [
                'status' => true,
                'code' => 200,
            ]
        ])->assertStatus(200);
    }

    public function testBookingStatusWaitingSuccess()
    {
        $headers = $this->getFauthHeaders();
        $book = Book::first();
        $user = $book->usersWaitingBook()->first();

        $newUpdate['book_id'] = $book->id;
        $newUpdate['status'] = config('model.book_user.status.done');
        $newUpdate['user_id'] = $user ? $user->id : $this->createUser()->id;

        $response = $this->call('POST', route('api.v0.books.booking', $book->id), ['item' => $newUpdate], [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code',
            ],
        ])->assertJson([
            'message' => [
                'status' => true,
                'code' => 200,
            ]
        ])->assertStatus(200);
    }

    public function testBookingWithNewUserSuccess()
    {
        $headers = $this->getFauthHeaders();
        $book = Book::first();
        $user = $this->createUser();

        $newUpdate['book_id'] = $book->id;
        $newUpdate['status'] = config('model.book_user.status.done');
        $newUpdate['user_id'] = $user->id;

        $response = $this->call('POST', route('api.v0.books.booking', $book->id), ['item' => $newUpdate], [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code',
            ],
        ])->assertJson([
            'message' => [
                'status' => true,
                'code' => 200,
            ]
        ])->assertStatus(200);
    }

    public function testBookingWithBookNotOwner()
    {
        $headers = $this->getFauthHeaders();
        $user = $this->createUser();

        $newUpdate['book_id'] = 0;
        $newUpdate['status'] = config('model.book_user.status.done');
        $newUpdate['user_id'] = $user->id;

        $response = $this->call('POST', route('api.v0.books.booking', 0), ['item' => $newUpdate], [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code', 'description'
            ],
        ])->assertJson([
            'message' => [
                'status' => false,
                'code' => 404,
            ]
        ])->assertStatus(404);
    }

    public function testBookingStatusWaitingWithGuest()
    {
        $headers = $this->getHeaders();
        $book = Book::first();
        $user = $book->usersWaitingBook()->first();

        $newUpdate['book_id'] = $book->id;
        $newUpdate['status'] = config('model.book_user.status.done');
        $newUpdate['user_id'] = $user ? $user->id : $this->createUser()->id;

        $response = $this->call('POST', route('api.v0.books.booking', $book->id), ['item' => $newUpdate], [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code', 'description'
            ],
        ])->assertJson([
            'message' => [
                'status' => false,
                'code' => 401,
            ]
        ])->assertStatus(401);
    }

    /* TEST REVIEW BOOK */

    public function testReviewBookSuccess()
    {
        $faker = Factory::create();
        $headers = $this->getFauthHeaders();
        $book = factory(Book::class)->create();

        $dataReview['content'] = $faker->sentence;
        $dataReview['star'] = $faker->numberBetween(1, 5);

        $response = $this->call('POST', route('api.v0.books.review', $book->id), ['item' => $dataReview], [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code',
            ],
        ])->assertJson([
            'message' => [
                'status' => true,
                'code' => 200,
            ]
        ])->assertStatus(200);
    }

    public function testReviewBookWithFieldsNull()
    {
        $headers = $this->getFauthHeaders();
        $book = factory(Book::class)->create();

        $response = $this->call('POST', route('api.v0.books.review', $book->id), ['item' => []], [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code', 'description'
            ],
        ])->assertJson([
            'message' => [
                'status' => false,
                'code' => 422,
            ]
        ])->assertStatus(422);
    }

    public function testReviewBookWithBookIdInvalid()
    {
        $headers = $this->getFauthHeaders();

        $response = $this->call('POST', route('api.v0.books.review', 0), ['item' => []], [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code', 'description'
            ],
        ])->assertJson([
            'message' => [
                'status' => false,
                'code' => 422,
            ]
        ])->assertStatus(422);
    }

    public function testReviewBookWithGuest()
    {
        $faker = Factory::create();
        $headers = $this->getHeaders();
        $book = factory(Book::class)->create();

        $dataReview['content'] = $faker->sentence;
        $dataReview['star'] = $faker->numberBetween(1, 5);

        $response = $this->call('POST', route('api.v0.books.review', $book->id), ['item' => $dataReview], [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code', 'description'
            ],
        ])->assertJson([
            'message' => [
                'status' => false,
                'code' => 401,
            ]
        ])->assertStatus(401);
    }

    /* TEST GET BOOKS */

    public function testGetBooksFilterByRatingSuccess()
    {
        $response = $this->call(
            'POST', route('api.v0.books.filters', ['field' => 'rating']), $this->dataFilterBook(), [], [], $this->getHeaders()
        );

        $response->assertJsonStructure([
            'item' => [
                'total', 'per_page', 'current_page', 'next_page', 'prev_page', 'data'
            ],
            'message' => [
                'status', 'code',
            ],
        ])->assertJson([
            'message' => [
                'status' => true,
                'code' => 200,
            ]
        ])->assertStatus(200);
    }

    public function testGetBooksFilterByLatestSuccess()
    {
        $response = $this->call(
            'POST', route('api.v0.books.filters', ['field' => 'latest']), $this->dataFilterBook(), [], [], $this->getHeaders()
        );

        $response->assertJsonStructure([
            'item' => [
                'total', 'per_page', 'current_page', 'next_page', 'prev_page', 'data'
            ],
            'message' => [
                'status', 'code',
            ],
        ])->assertJson([
            'message' => [
                'status' => true,
                'code' => 200,
            ]
        ])->assertStatus(200);
    }

    public function testGetBooksFilterByViewSuccess()
    {
        $response = $this->call(
            'POST', route('api.v0.books.filters', ['field' => 'view']), $this->dataFilterBook(), [], [], $this->getHeaders()
        );

        $response->assertJsonStructure([
            'item' => [
                'total', 'per_page', 'current_page', 'next_page', 'prev_page', 'data'
            ],
            'message' => [
                'status', 'code',
            ],
        ])->assertJson([
            'message' => [
                'status' => true,
                'code' => 200,
            ]
        ])->assertStatus(200);
    }

    public function testGetBooksFilterInvalid()
    {
        $input = [
            'filters' => 'a',
            'sort' => [
                'key' => 'a',
                'order_by' => 'a',
            ],
        ];
        $response = $this->call('POST', route('api.v0.books.filters', ['field' => 'viewa']), $input, [], [], $this->getHeaders());
        $response->assertJsonStructure([
            'message' => [
                'status', 'code', 'description',
            ],
        ])->assertJson([
            'message' => [
                'status' => false,
                'code' => 422,
            ]
        ])->assertStatus(422);
    }

    /* TEST STORE BOOKS */

    public function testStoreBookSuccess()
    {
        $headers = $this->getFauthHeaders();
        $dataBook = factory(Book::class)->make()->toArray();
        $dataBook['category_id'] = factory(Category::class)->create()->id;
        $dataBook['office_id'] = factory(Office::class)->create()->id;
        $dataBook['medias'][0]['file'] = UploadedFile::fake()->image(str_random(20) . '.jpg', 100, 100)->size(100);

        $response = $this->call('POST', route('api.v0.books.store'), $dataBook, [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code',
            ],
        ])->assertJson([
            'message' => [
                'status' => true,
                'code' => 200,
            ]
        ])->assertStatus(200);
    }

    public function testStoreBookWithFieldsNull()
    {
        $headers = $this->getFauthHeaders();

        $response = $this->call('POST', route('api.v0.books.store'), [], [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code', 'description'
            ],
        ])->assertJson([
            'message' => [
                'status' => false,
                'code' => 422,
            ]
        ])->assertStatus(422);
    }

    public function testStoreBookWithGuest()
    {
        $headers = $this->getHeaders();
        $dataBook = factory(Book::class)->make()->toArray();
        $dataBook['category_id'] = factory(Category::class)->create()->id;
        $dataBook['office_id'] = factory(Office::class)->create()->id;
        $dataBook['medias'][0]['file'] = UploadedFile::fake()->image(str_random(20) . '.jpg', 100, 100)->size(100);

        $response = $this->call('POST', route('api.v0.books.store'), $dataBook, [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code', 'description'
            ],
        ])->assertJson([
            'message' => [
                'status' => false,
                'code' => 401,
            ]
        ])->assertStatus(401);
    }

    /* TEST UPDATE BOOKS */

    public function testUpdateBookNotOwner()
    {
        $headers = $this->getFauthHeaders();
        $bookId = factory(Book::class)->create()->id;
        $dataBook = factory(Book::class)->make()->toArray();
        $dataBook['category_id'] = factory(Category::class)->create()->id;
        $dataBook['office_id'] = factory(Office::class)->create()->id;
        $dataBook['medias'][0]['file'] = UploadedFile::fake()->image(str_random(20) . '.jpg', 100, 100)->size(100);

        $response = $this->call('PUT', route('api.v0.books.update', $bookId), $dataBook, [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code',
            ],
        ])->assertJson([
            'message' => [
                'status' => false,
                'code' => 400,
            ]
        ])->assertStatus(400);
    }

    public function testUpdateBookWithFieldsNull()
    {
        $headers = $this->getFauthHeaders();
        $bookId = factory(Book::class)->create()->id;

        $response = $this->call('PUT', route('api.v0.books.update', $bookId), [], [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code', 'description'
            ],
        ])->assertJson([
            'message' => [
                'status' => false,
                'code' => 422,
            ]
        ])->assertStatus(422);
    }

    public function testUpdateBookWithGuest()
    {
        $headers = $this->getHeaders();
        $bookId = factory(Book::class)->create()->id;
        $dataBook = factory(Book::class)->make()->toArray();
        $dataBook['category_id'] = factory(Category::class)->create()->id;
        $dataBook['office_id'] = factory(Office::class)->create()->id;
        $dataBook['medias'][0]['file'] = UploadedFile::fake()->image(str_random(20) . '.jpg', 100, 100)->size(100);

        $response = $this->call('PUT', route('api.v0.books.update', $bookId), $dataBook, [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code', 'description'
            ],
        ])->assertJson([
            'message' => [
                'status' => false,
                'code' => 401,
            ]
        ])->assertStatus(401);
    }

    /* TEST STORE BOOKS */

    public function testDeleteBookWithInvalidBookId()
    {
        $headers = $this->getFauthHeaders();

        $response = $this->call('DELETE', route('api.v0.books.destroy', 'xxx'), [], [], [], $headers);
        $response->assertJsonStructure([
            'message' => [
                'status', 'code', 'description'
            ],
        ])->assertJson([
            'message' => [
                'status' => false,
                'code' => 404,
            ]
        ])->assertStatus(404);
    }

    public function testDeleteBookWithGuest()
    {
        $headers = $this->getHeaders();
        $bookId = factory(Book::class)->create()->id;

        $response = $this->call('DELETE', route('api.v0.books.destroy', $bookId), [], [], [], $headers);

        $response->assertJsonStructure([
            'message' => [
                'status', 'code', 'description'
            ],
        ])->assertJson([
            'message' => [
                'status' => false,
                'code' => 401,
            ]
        ])->assertStatus(401);
    }

    /* TEST GET BOOK BY CATEGORY */

    public function testGetBooksByCategorySuccess()
    {
        $categoryId = factory(Category::class)->create()->id;
        $response = $this->call('GET', route('api.v0.books.category', $categoryId), [], [], [], $this->getHeaders());

        $response->assertJsonStructure([
            'items' => [
                'total', 'per_page', 'current_page', 'next_page', 'prev_page', 'data'
            ],
            'message' => [
                'status', 'code',
            ],
        ])->assertJson([
            'message' => [
                'status' => true,
                'code' => 200,
            ]
        ])->assertStatus(200);
    }

    public function testGetBooksByCategoryWithIdInvalid()
    {
        $response = $this->call('GET', route('api.v0.books.category', 0), [], [], [], $this->getHeaders());

        $response->assertJsonStructure([
            'message' => [
                'status', 'code', 'description'
            ],
        ])->assertJson([
            'message' => [
                'status' => false,
                'code' => 404,
                'description' => [translate('exception.not_found')]
            ]
        ])->assertStatus(404);
    }
}
