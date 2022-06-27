<?php

declare(strict_types=1);

namespace Momentum\Modal;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Inertia\Inertia;

class Modal implements Responsable
{
    protected string $baseURL;

    public function __construct(
        protected string $component,
        protected array $props = []
    ) {
    }

    public function baseRoute(string $name, mixed $parameters = [], bool $absolute = true): static
    {
        $this->baseURL = route($name, $parameters, $absolute);

        return $this;
    }

    public function basePageRoute(string $name, mixed $parameters = [], bool $absolute = true): static
    {
        return $this->baseRoute($name, $parameters, $absolute);
    }

    public function with(array $props): static
    {
        $this->props = $props;

        return $this;
    }

    public function render(): mixed
    {
        /** @phpstan-ignore-next-line */
        inertia()->share(['modal' => $this->component()]);

        // render background component on first visit
        if (request()->header('X-Inertia') && request()->header('X-Inertia-Partial-Component')) {
            /** @phpstan-ignore-next-line */
            return Inertia::render(request()->header('X-Inertia-Partial-Component'));
        }

        /** @var Request $originalRequest */
        $originalRequest = app('request');

        $request = $originalRequest->create(
            $this->backgroundURL(),
            Request::METHOD_GET,
            $originalRequest->query->all()
        );

        // swap request to preserve original query
        app()->instance('request', $request);

        $baseRoute = Route::getRoutes()->match($request);

        return app()->call($baseRoute->getAction('uses'), $baseRoute->parameters());
    }

    protected function component(): array
    {
        return [
            'component' => $this->component,
            'baseURL' => $this->baseURL,
            'redirectURL' => $this->redirectURL(),
            'props' => $this->props,
            'key' => request()->header('X-Inertia-Modal-Key', Str::uuid()->toString()),
            'nonce' => Str::uuid()->toString(),
        ];
    }

    protected function backgroundURL(): string
    {
        if (request()->header('X-Inertia')) {
            return $this->redirectURL();
        }

        return $this->baseURL;
    }

    protected function redirectURL(): string
    {
        if (request()->header('X-Inertia-Modal-Redirect')) {
            /** @phpstan-ignore-next-line */
            return request()->header('X-Inertia-Modal-Redirect');
        }

        if (url()->previous() != url()->current()) {
            return url()->previous();
        }

        return $this->baseURL;
    }

    public function toResponse($request)
    {
        $response = $this->render();

        if ($response instanceof Responsable) {
            return $response->toResponse($request);
        }

        return $response;
    }
}
