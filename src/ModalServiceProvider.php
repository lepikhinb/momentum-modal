<?php

declare(strict_types=1);

namespace Momentum\Modal;

use Illuminate\Support\ServiceProvider;
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
    }
}
