<?php

declare(strict_types=1);

namespace Momentum\Modal;

use Illuminate\Support\ServiceProvider;
use Inertia\Response;
use Inertia\ResponseFactory;

class ModalServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        ResponseFactory::macro('modal', function (
            string $component,
            array $props = []
        ) {
            return new Modal($component, $props);
        });

        $this->registerCompatibilityMacros();
    }

    /**
     * Extra macros for compatibility with the unreleased official feature
     * 
     * @return void 
     */
    public function registerCompatibilityMacros(): void
    {
        ResponseFactory::macro('dialog', function (
            string $component,
            array $props = []
        ) {
            return new Modal($component, $props);
        });

        Response::macro('stackable', function () {
            return new Modal($this->component, $this->props);
        });
    }
}
