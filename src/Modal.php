<?php

namespace Momentum\Modal;

use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Response;

class Modal implements Responsable
{
    protected string $baseURL;

    public function __construct(
        protected ?string $component = null,
        protected array $props = []
    ) {
    }

    public function baseRoute(string $name, mixed $parameters = [], bool $absolute = true): Response
    {
        $this->baseURL = route($name, $parameters, $absolute);

        return $this->render();
    }

    public function props(array $props): static
    {
        $this->props = $props;

        return $this;
    }

    public function render(): Response|RedirectResponse
    {
        inertia()->share(['modal' => $this->component()]);

        /** @var Request $originalRequest */
        $originalRequest = app('request');

        $request = Request::create(
            $this->baseURL,
            Request::METHOD_GET,
            $originalRequest->query(),
            $originalRequest->cookie(),
            $originalRequest->file(),
            $originalRequest->server(),
            $originalRequest->getContent()
        );

        $baseRoute = Route::getRoutes()->match($request);

        $response = app()->call($baseRoute->getAction('uses'));

        return $response;
    }

    public function component(): array
    {
        return [
            'component' => $this->component,
            'baseURL' => $this->baseURL,
            'props' => $this->props,
            'inertia' => request()->inertia(),
        ];
    }

    public function toResponse($request)
    {
        return $this->render()->toResponse($request);
    }
}
