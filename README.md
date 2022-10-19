# Momentum Modal

Momentum Modal is a Laravel package that lets you implement backend-driven modal dialogs for Inertia apps.

Define modal routes on the backend and dynamically render them when you visit a dialog route.

Check out the [demo app](https://modal.advanced-inertia.com) demonstrating the Modal package in action.

- [**Installation**](#installation)
    - [**Laravel**](#laravel)
    - [**Vue 3**](#vue-3)
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

> The frontend package is only for Vue 3 now due to its adoption within the Laravel community.

Install the frontend package.

```bash
npm i momentum-modal
# or
yarn add momentum-modal
```

> **Warning**
> The package utilizes `axios` under the hood. If your app is already using `axios` as a dependency, make sure to lock it to the same version Inertia uses.
> ```bash
> npm i axios@0.21.4
> ```

## Setup

[Modal](https://github.com/lepikhinb/momentum-modal-plugin) is a **headless** component, meaning you have full control over its look, whether it's a modal dialog or a slide-over panel. You are free to use any 3rd-party solutions to power your modals, such as [Headless UI](https://github.com/tailwindlabs/headlessui).

Put the `Modal` component somewhere within the layout.

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

Set up a `modal` plugin with the same component resolver you use to render Inertia pages.

### Vite

```javascript
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
```

### Laravel Mix

```javascript
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

Find the example frontend implementation [here](https://github.com/lepikhinb/momentum-modal-plugin/tree/master/examples).

## Advanced Inertia

[<img src="https://advanced-inertia.com/og5.png" width="420px" />](https://advanced-inertia.com)

Make Inertia-powered frontend a breeze to build and maintain with my upcoming book [Advanced Inertia](https://advanced-inertia.com/). Join the waitlist and get **20% off** when the book is out.

## Momentum

Momentum is a set of packages designed to bring back the feeling of working on a single codebase to Inertia-powered apps.

- [Modal](https://github.com/lepikhinb/momentum-modal) — Build dynamic modal dialogs for Inertia apps
- [Preflight](https://github.com/lepikhinb/momentum-preflight) — Realtime backend-driven validation for Inertia apps
- [Paginator](https://github.com/lepikhinb/momentum-paginator) — Headless wrapper around Laravel Pagination
- State — Laravel package to manage the frontend state of Inertia apps *(coming soon)*
- Router — Frontend plugin to use Laravel routes with Inertia *(coming soon)*
- Permissions — Frontend plugin to use your Laravel permission with Inertia *(coming soon)*
- Locale — Use your Laravel translation files with Inertia *(coming soon)*

## Credits

- [Boris Lepikhin](https://twitter.com/lepikhinb)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
