<?php

namespace Tests\Feature;

use Faker\Factory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Eloquent\Category;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    /* TEST GET BOOKS OF CURRENT USER */

    public function testGetDataBookByCurrentUserSuccess()
    {
        $faker = Factory::create();
        $action = $faker->randomElement(array_merge(
            [config('model.user_sharing_book')], array_keys(config('model.book_user.status'))
        ));

        $response = $this->call('GET', route('api.v0.users.book', $action), [], [], [], $this->getFauthHeaders());

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

    public function testGetDataBookByCurrentUserWithGuest()
    {
        $response = $this->call('GET', route('api.v0.users.book', 'action'), [], [], [], $this->getHeaders());

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

    public function testGetDataBookByCurrentUserWithActionException()
    {
        $response = $this->call('GET', route('api.v0.users.book', 'action'), [], [], [], $this->getFauthHeaders());

        $response->assertJsonStructure([
            'message' => [
                'status', 'code', 'description'
            ],
        ])->assertJson([
            'message' => [
                'status' => false,
                'code' => 500,
                'description' => [translate('exception.action')]
            ]
        ])->assertStatus(500);
    }

    /* TEST GET USER PROFILE */

    public function testGetUserProfileSuccess()
    {
        $headers = $this->getFauthHeaders();

        $response = $this->call('GET', route('api.v0.user.profile'), [], [], [], $headers);
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

    public function testGetUserProfileWithGuest()
    {
        $headers = $this->getHeaders();

        $response = $this->call('GET', route('api.v0.user.profile'), [], [], [], $headers);
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

    /* TEST ADD TAGS FOR USERS */

    public function testAddTagsSuccess()
    {
        $headers = $this->getFauthHeaders();
        $categoryId = factory(Category::class)->create()->id;
        $data['tags'] = $categoryId;

        $response = $this->call('POST', route('api.v0.user.add.tags'), ['item' => $data], [], [], $headers);
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

    public function testAddTagsWithGuest()
    {
        $headers = $this->getHeaders();
        $categoryId = factory(Category::class)->create()->id;
        $data['tags'] = $categoryId;

        $response = $this->call('POST', route('api.v0.user.add.tags'), ['item' => $data], [], [], $headers);
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

    public function testAddTagsWithFieldsNull()
    {
        $headers = $this->getFauthHeaders();

        $response = $this->call('POST', route('api.v0.user.add.tags'), [], [], [], $headers);
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
}
