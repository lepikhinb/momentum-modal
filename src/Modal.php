<?php

declare(strict_types=1);

namespace Momentum\Modal;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Route;
use Illuminate\Support\Str;

class Modal implements Responsable
{
    protected string $baseURL;
    protected array $props = [];

    public function __construct(
        protected string $component,
        array|Arrayable $props
    ) {
        $this->with($props);
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

    public function with(array|Arrayable $props): static
    {
        $this->props = $props instanceof Arrayable ? $props->toArray() : $props;

        return $this;
    }

    public function render(): mixed
    {
        $flatProps = [];
        foreach ($this->props as $key => $prop) {
            $flatProps['modal.props.'.$key] = $prop;
        }
        /** @phpstan-ignore-next-line */
        inertia()->share(['modal' => $this->component(), ...$flatProps]);

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

        /** @var \Illuminate\Routing\Router */
        $router = app('router');

        $baseRoute = $router->getRoutes()->match($request);

        $request->headers->replace($originalRequest->headers->all());

        $request->setJson($originalRequest->json())
            ->setUserResolver(fn () => $originalRequest->getUserResolver())
            ->setRouteResolver(fn () => $baseRoute)
            ->setLaravelSession($originalRequest->session());

        app()->instance('request', $request);

        return $this->handleRoute($request, $baseRoute);
    }

    protected function handleRoute(Request $request, Route $route): mixed
    {
        /** @var \Illuminate\Routing\Router */
        $router = app('router');

        $middleware = new SubstituteBindings($router);

        return $middleware->handle(
            $request,
            fn () => $route->run()
        );
    }

    protected function component(): array
    {
        return [
            'component' => $this->component,
            'baseURL' => $this->baseURL,
            'redirectURL' => $this->redirectURL(),
            'key' => request()->header('X-Inertia-Modal-Key') ?? Str::uuid()->toString(),
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

        if (request()->header('X-Inertia') && $referer && $referer != url()->current()) {
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
