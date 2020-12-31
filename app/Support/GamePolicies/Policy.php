<?php

namespace App\Support\GamePolicies;

use App\Models\Game;
use App\Models\Round;

class Policy
{
    public function startGame(Game $game)
    {
    }

    public function roundAction(Round $round, array $options = null)
    {
    }

    public function endRound(Round $round)
    {
    }

}
