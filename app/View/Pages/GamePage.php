<?php

namespace App\View\Pages;

use App\Models\Game;

class GamePage
{
    public function __invoke(Game $game)
    {
        return view('GamePage', [
            'game' => $game,
        ]);
    }
}
