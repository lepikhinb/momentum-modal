<?php

declare(strict_types=1);

use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Route;
use Inertia\Testing\AssertableInertia;
use Momentum\Modal\Tests\Stubs\ExampleController;
use Momentum\Modal\Tests\Stubs\ExampleMiddleware;
use function Pest\Laravel\from;
use function Pest\Laravel\get;

beforeEach(function () {
    Route::middleware([StartSession::class, ExampleMiddleware::class])
        ->group(function () {
            Route::get('/', fn () => '')->name('home');
            Route::get('{user}', [ExampleController::class, 'user'])->name('users.show');
            Route::get('{user}/{tweet}', [ExampleController::class, 'tweet'])->name('users.tweets.show');
        });
});

test('modals can be rendered', function () {
    get(route('users.tweets.show', [user(), tweet()]))
        ->assertInertia(function (AssertableInertia $page) {
            $page->component('Users/Show')
                ->where('modal.baseURL', route('users.show', user()))
                ->where('modal.component', 'Tweets/Show')
                ->where('modal.props', [
                    'user' => user(),
                    'tweet' => tweet(),
                ]);
        });
});

test('preserve background on inertia visits', function () {
    from(route('home'))
        ->get(route('users.tweets.show', [user(), tweet()]))
        ->assertInertia(function (AssertableInertia $page) {
            $page->component('Users/Show')
                ->where('user', user())
                ->where('modal.redirectURL', route('home'))
                ->where('modal.baseURL', route('users.show', user()));
        });
});

test('preserve query string for parent componentы', function () {
    $fromURL = route('users.show', ['user' => user(), 'page' => 3]);

    from($fromURL)
        ->get(route('users.tweets.show', [user(), tweet()]), [
            'X-Inertia' => true,
            'X-Inertia-Modal-Redirect' => $fromURL,
        ])
        ->assertJsonPath('component', 'Users/Show')
        ->assertJsonPath('props.page', '3')
        ->assertJsonPath('props.modal.redirectURL', $fromURL);
});
