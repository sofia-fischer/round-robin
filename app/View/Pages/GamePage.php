<?php

namespace App\View\Pages;

use App\Models\Game;
use App\Models\Group;

class GamePage
{
    public function __invoke(Group $group, Game $game)
    {
        if (!$game->authenticatedPlayer) {
            return redirect()->route('WelcomePage');
        }

        return view('GamePage', [
            'game'  => $game,
            'group' => $group,
        ]);
    }
}
