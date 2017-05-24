<?php

namespace Tests\Feature\ControllerTests\Api;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use DatabaseTransactions;

    public function testListBookInHomePageSuccess()
    {
        $headers = $this->getHeaders();

        $response = $this->call('GET', 'api/v0/home', [], [], [], $headers);
        $response->assertJsonStructure([
            'items' => [
                ['key', 'title', 'data']
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
}
