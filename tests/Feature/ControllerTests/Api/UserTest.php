<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Eloquent\Book;
use Faker\Factory;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    /*TEST GET MY READING BOOKS*/

    public function testGetReadingBooksByCurrentUserSuccess()
    {
        $response = $this->call('GET', route('api.v0.users.bookReading', []), [], [], [], $this->getFauthHeaders());

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

    public function testGetReadingBooksWithGuest()
    {
        $response = $this->call('GET', route('api.v0.users.bookReading', []), [], [], [], $this->getHeaders());

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
}
