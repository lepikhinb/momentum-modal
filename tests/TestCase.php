<?php

declare(strict_types=1);

namespace Momentum\Modal\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Inertia\Inertia;
use Inertia\ServiceProvider as InertiaServiceProvider;
use Momentum\Modal\ModalServiceProvider;
use Orchestra\Testbench\TestCase as TestbenchTestCase;

class TestCase extends TestbenchTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        View::addLocation(__DIR__ . '/Stubs');
        Inertia::setRootView('app');
        config()->set('inertia.testing.ensure_pages_exist', false);
        config()->set('inertia.testing.page_paths', [realpath(__DIR__)]);
    }

    public function defineDatabaseMigrations()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username');
            $table->timestamps();
        });

        Schema::create('tweets', function (Blueprint $table) {
            $table->increments('id');
            $table->foreignId('user_id');
            $table->string('body');
            $table->timestamps();
        });
    }

    protected function getPackageProviders($app)
    {
        return [
            InertiaServiceProvider::class,
            ModalServiceProvider::class,
        ];
    }
}
