<?php

declare(strict_types=1);

namespace Momentum\Modal;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class Modal implements Responsable
{
    protected string $baseURL;

    public function __construct(
        protected ?string $component = null,
        protected array $props = []
    ) {
    }

    public function baseRoute(string $name, mixed $parameters = [], bool $absolute = true): static
    {
        $this->baseURL = route($name, $parameters, $absolute);

        return $this;
    }

    public function props(array $props): static
    {
        $this->props = $props;

        return $this;
    }

    public function render(): mixed
    {
        /** @phpstan-ignore-next-line */
        inertia()->share(['modal' => $this->component()]);

        /** @var Request $originalRequest */
        $originalRequest = app('request');

        $request = Request::create(
            $this->baseURL,
            Request::METHOD_GET,
            $originalRequest->query->all(),
            $originalRequest->cookies->all(),
            $originalRequest->files->all(),
            $originalRequest->server->all(),
            $originalRequest->getContent()
        );

        $baseRoute = Route::getRoutes()->match($request);

        return app()->call($baseRoute->getAction('uses'));
    }

    protected function component(): array
    {
        return [
            'component' => $this->component,
            'baseURL' => $this->baseURL,
            'props' => $this->props,
            'inertia' => request()->inertia(), // @phpstan-ignore-line
            'key' => request()->header('X-Inertia-Modal', Str::uuid()->toString()),
        ];
    }

    public function toResponse($request)
    {
        return $this->render()->toResponse($request);
    }
}
