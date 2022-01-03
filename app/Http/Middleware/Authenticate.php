<?php

namespace App\Http\Middleware;

use App\Models\Game;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if ($request->expectsJson()) {
            return;
        }

        if (! $request->route('game')) {
            return route('auth.login');
        }

        $game = Game::query()->where('uuid', $request->route('game'))->first();

        if (! $game) {
            return route('auth.login');
        }

        return route('auth.login', ['token' => $game->token]);
    }
}
