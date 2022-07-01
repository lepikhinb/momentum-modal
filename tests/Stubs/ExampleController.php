<?php

declare(strict_types=1);

namespace Momentum\Modal\Tests\Stubs;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ExampleController
{
    public function user(Request $request, User $user)
    {
        return Inertia::render('Users/Show', ['user' => $user, 'page' => $request->input('page')]);
    }

    public function tweet(User $user, Tweet $tweet)
    {
        return Inertia::modal('Tweets/Show', [
            'user' => $user,
            'tweet' => $tweet,
        ])
            ->baseRoute('users.show', $user);
    }

    public function differentParameters(User $user, Tweet $tweet)
    {
        return Inertia::modal('Tweets/Show', [
            'user' => $user,
            'tweet' => $tweet,
        ])
            ->baseRoute('users.show', User::where('id', '<>', $user->id)->first());
    }

    public function rawUser(string $user)
    {
        return Inertia::render('Users/Show', ['user' => $user]);
    }

    public function rawTweet($user, $tweet)
    {
        return Inertia::modal('Tweets/Show', [
            'user' => $user,
            'tweet' => $tweet,
        ])
            ->baseRoute('raw.users.show', $user);
    }
}
