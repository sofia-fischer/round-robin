<?php

namespace App\Support\GamePolicies;

use App\Models\Game;
use App\Models\Player;
use App\Models\Round;

class Policy
{
    public function startGame(Game $game)
    {
    }

    public function roundAction(Round $round, array $options = [])
    {
    }

    public function endRound(Round $round)
    {
    }

    public function playerJoined(Player $player, Game $game)
    {
    }

}
