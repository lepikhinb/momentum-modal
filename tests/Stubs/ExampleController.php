<?php

declare(strict_types=1);

namespace Momentum\Modal\Tests\Stubs;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ExampleController
{
    public function user(Request $request, $user)
    {
        return Inertia::render('Users/Show', ['user' => $user, 'page' => $request->input('page')]);
    }

    public function tweet($user, $tweet)
    {
        return Inertia::modal('Tweets/Show', [
            'user' => $user, 'tweet' => $tweet,
        ])
            ->baseRoute('users.show', $user);
    }
}
