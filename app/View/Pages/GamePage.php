<?php

namespace App\View\Pages;

use App\Models\Game;
use App\Models\Group;

class GamePage
{
    public function __invoke(Group $group, Game $game)
    {
        return view('GamePage', [
            'game'  => $game,
            'group' => $group,
        ]);
    }
}
