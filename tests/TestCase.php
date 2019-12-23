<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Mockery;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function myMock($class)
    {
        $mock = Mockery::mock($class);
        $this->app->instance($class, $mock);

        return $mock;
    }

    protected function myMockPartial($class)
    {
        $mock = Mockery::mock($class)->makePartial();
        $this->app->instance($class, $mock);

        return $mock;
    }
}
