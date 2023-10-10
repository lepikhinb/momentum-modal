<?php

declare(strict_types=1);

namespace Momentum\Modal;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\ServiceProvider;
use Inertia\Response;
use Inertia\ResponseFactory;

class ModalServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        ResponseFactory::macro('modal', function (
            string $component,
            array|Arrayable $props = []
        ) {
            return app()->makeWith(Modal::class, compact('component', 'props'));
        });

        $this->registerCompatibilityMacros();
    }

    /**
     * Extra macros for compatibility with the unreleased official feature.
     */
    public function registerCompatibilityMacros(): void
    {
        ResponseFactory::macro('dialog', function (
            string $component,
            array|Arrayable $props = []
        ) {
            return app()->makeWith(Modal::class, compact('component', 'props'));
        });

        Response::macro('stackable', function () {
            return app()->makeWith(Modal::class, [
                'component' => $this->component,
                'props' => $this->props,
            ]);
        });
    }
}
