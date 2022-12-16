<?php

namespace Tests;

use App\Base\Kernel;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\TestCase as BaseTestCase;
use ReflectionProperty;

class TestCase extends BaseTestCase
{
    public Kernel $app;
    public Generator $faker;

    protected function setUp(): void
    {
        $reflection = new ReflectionProperty($this, 'app');

        if (!$reflection->isInitialized($this)) {
            $this->app = app();
            $this->faker = Factory::create($this->app->config()->FAKER_LOCALE);
        }
    }

    protected function tearDown(): void
    {
        if (method_exists($this, 'truncateTables')) {
            $this->truncateTables();
        }
    }
}
