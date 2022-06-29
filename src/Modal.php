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
            return inertia()->render(request()->header('X-Inertia-Partial-Component'));
        }

        /** @var Request $originalRequest */
        $originalRequest = app('request');

        $request = Request::create(
            $this->redirectURL(),
            Request::METHOD_GET,
            $originalRequest->query->all(),
            $originalRequest->cookies->all(),
            $originalRequest->files->all(),
            $originalRequest->server->all(),
            $originalRequest->getContent()
        );

        $baseRoute = Route::getRoutes()->match($request);

        $request->headers->replace($originalRequest->headers->all());

        $request->setJson($originalRequest->json())
            ->setUserResolver(fn () => $originalRequest->getUserResolver())
            ->setRouteResolver(fn () => $baseRoute)
            ->setLaravelSession($originalRequest->session());

        app()->instance('request', $request);

        return app()->call($baseRoute->getAction('uses'), Route::current()?->parameters() ?? []);
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

    protected function redirectURL(): string
    {
        if (request()->header('X-Inertia-Modal-Redirect')) {
            /** @phpstan-ignore-next-line */
            return request()->header('X-Inertia-Modal-Redirect');
        }

        $referer = request()->headers->get('referer');

        if ($referer && $referer != url()->current()) {
            return $referer;
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
