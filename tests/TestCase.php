<?php

declare(strict_types=1);

namespace Momentum\Modal\Tests;

use Inertia\ServiceProvider as InertiaServiceProvider;
use Momentum\Modal\ModalServiceProvider;
use Orchestra\Testbench\TestCase as TestbenchTestCase;

class TestCase extends TestbenchTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            InertiaServiceProvider::class,
            ModalServiceProvider::class,
        ];
    }
}
