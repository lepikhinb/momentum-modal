<?php

namespace Momentum\Modal\Tests;

use Orchestra\Testbench\TestCase as TestbenchTestCase;
use Momentum\Modal\ModalServiceProvider;

class TestCase extends TestbenchTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            ModalServiceProvider::class,
        ];
    }
}
