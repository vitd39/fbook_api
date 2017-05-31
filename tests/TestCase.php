<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use App\Eloquent\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();
        $this->artisan('migrate');
        $this->artisan('db:seed', ['--class' => 'TestDatabaseSeeder']);
    }

    public function getHeaders($header = [])
    {
        $default = [
            'Accept' => 'application/json',
        ];

        $headers = count($header) ? array_merge($default, $header) : $default;

        return $this->transformHeadersToServerVars($headers);
    }

    public function createUser()
    {
        return factory(User::class)->create();
    }
}
