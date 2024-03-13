# Momentum Modal

Momentum Modal is a Laravel package that lets you implement backend-driven modal dialogs for Inertia apps.

Define modal routes on the backend and dynamically render them when you visit a dialog route.

Check out the [demo app](https://modal.advanced-inertia.com) demonstrating the Modal package in action.

- [**Installation**](#installation)
    - [**Laravel**](#laravel)
    - [**Vue 3**](#vue-3)
    - [**React**](#react)
- [**Setup**](#setup)
    - [**Vite**](#vite)
    - [**Laravel Mix**](#laravel-mix)
- [**Usage**](#usage)
- [**Advanced Inertia**](#advanced-inertia)
- [**Momentum**](#momentum)

## Installation

### Laravel

Install the package into your Laravel app.

```bash
composer require based/momentum-modal
```

### Vue 3

Install the frontend package.

```bash
npm i momentum-modal
# or
yarn add momentum-modal
```

> **Warning**
> The package utilizes `axios` under the hood. If your app is already using `axios` as a dependency, make sure to lock it to the same version Inertia uses.
>
> ```bash
> npm i axios@1.2.0
> ```

### React

Install the frontend package.

```bash
npm i momentum-modal-react
# or
yarn add momentum-modal-react
```

> **Warning**
> The package utilizes `axios` under the hood. If your app is already using `axios` as a dependency, make sure to lock it to the same version Inertia uses.
>
> ```bash
> npm i axios@1.6.0
> ```

## Setup

[Modal](https://github.com/lepikhinb/momentum-modal-plugin) is a **headless** component, meaning you have full control over its look, whether it's a modal dialog or a slide-over panel. You are free to use any 3rd-party solutions to power your modals, such as [Headless UI](https://github.com/tailwindlabs/headlessui).

Put the `Modal` component somewhere within the layout.

### Vue 3 setup

```vue
<script setup>
import { Modal } from 'momentum-modal'
</script>

<template>
    <div>
        <!-- layout -->
        <Modal />
    </div>
</template>
```

### React setup

```jsx
import {Modal} from 'momentum-modal-react';

export function Layout({children}) {
  return (
    <>
      {children}
      <Modal />
    </>
  );
}
```

Set up a `modal` plugin with the same component resolver you use to render Inertia pages.

### Vite

```javascript
// Vue
import { modal } from "momentum-modal"

createInertiaApp({
  resolve: (name) => resolvePageComponent(name, import.meta.glob("./Pages/**/*.vue")),
  setup({ el, app, props, plugin }) {
    createApp({ render: () => h(app, props) })
      .use(modal, {
        resolve: (name) => resolvePageComponent(name, import.meta.glob("./Pages/**/*.vue")),
      })
      .use(plugin)
      .mount(el)
  }
})

// React
globalThis.resolveMomentumModal = (name) => {
  const pages = import.meta.glob('./Pages/**/*.jsx', {eager: true});
  return pages[`./Pages/${name}.jsx`];
};

createInertiaApp({
  resolve: (name) => {
    const pages = import.meta.glob('./Pages/**/*.jsx', {eager: true});
    return pages[`./Pages/${name}.jsx`];
  },
  setup({el, App, props}) {
    createRoot(el).render(<App {...props} />);
  },
});
```

### Laravel Mix

```javascript
// Vue
import { modal } from "momentum-modal"

createInertiaApp({
  resolve: (name) => require(`./Pages/${name}`),
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(modal, {
        resolve: (name) => import(`./Pages/${name}`),
      })
      .use(plugin)
      .mount(el)
  }
})

// React
globalThis.resolveMomentumModal = (name) => require(`./Pages/${name}`);

createInertiaApp({
  resolve: (name) => require(`./Pages/${name}`),
  setup({el, App, props}) {
    createRoot(el).render(<App {...props} />);
  },
});
```

## Usage

Modals have their own routes, letting you access them even via direct URLs. Define routes for your modal pages.

```php
// background context / base page
Route::get('{user}', ShowUser::class)
    ->name('users.show');

// modal route
Route::get('{user}/{tweet}', ShowTweet::class)
    ->name('users.tweets.show');
```

Render a modal from a controller. Specify the `base` route to render the background when the modal is accessed directly.

```php
class ShowTweet extends Controller
{
    public function __invoke(User $user, Tweet $tweet)
    {
        return Inertia::modal('Tweets/Show')
            ->with([
                'user' => $user,
                'tweet' => $tweet,
            ])
            ->baseRoute('users.show', $user);
    }
}
```

Find the example Vue 3 implementation [here](https://github.com/lepikhinb/momentum-modal-plugin/tree/master/examples).

### Using a custom Modal class

If you need to customize the Modal class - for example, to tweak the modal response - you can do so by binding a different implementation to the service container:

```php
class CustomModal extends Momentum\Modal\Modal
{
    public function render($component, $props = [])
    {
        return parent::render($component, $props)
            ->withViewData(['foo' => 'bar']);
    }
}

app()->bind(Momentum\Modal\Modal::class, CustomModal::class);
```

## Advanced Inertia

[<img src="https://advanced-inertia.com/og.png" width="420px" />](https://advanced-inertia.com)

Take your Inertia.js skills to the next level with my book [Advanced Inertia](https://advanced-inertia.com/).
Learn advanced concepts and make apps with Laravel and Inertia.js a breeze to build and maintain.

## Momentum

Momentum is a set of packages designed to improve your experience building Inertia-powered apps.

- [Modal](https://github.com/lepikhinb/momentum-modal) — Build dynamic modal dialogs for Inertia apps
- [Preflight](https://github.com/lepikhinb/momentum-preflight) — Realtime backend-driven validation for Inertia apps
- [Paginator](https://github.com/lepikhinb/momentum-paginator) — Headless wrapper around Laravel Pagination
- [Trail](https://github.com/lepikhinb/momentum-trail) — Frontend package to use Laravel routes with Inertia
- [Lock](https://github.com/lepikhinb/momentum-lock) — Frontend package to use Laravel permissions with Inertia
- [Layout](https://github.com/lepikhinb/momentum-layout) — Persistent layouts for Vue 3 apps
- [Vite Plugin Watch](https://github.com/lepikhinb/vite-plugin-watch) — Vite plugin to run shell commands on file changes

## Credits

- [Boris Lepikhin](https://twitter.com/lepikhinb)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
