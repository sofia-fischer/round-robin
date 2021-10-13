<?php

namespace App\View\Pages;

use App\Models\Game;
use App\Models\User;

class WelcomePage
{
    public function __invoke(Game $game = null)
    {
        // clean up database
        User::whereNull('email')->where('created_at', '<', now()->subWeek())->delete();

        return view('WelcomePage', ['game' => $game->uuid ?? null]);
    }
}
