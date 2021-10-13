<?php

namespace App\Support\Interfaces;

use App\Models\Game;
use App\Models\Round;
use App\Models\Player;

interface Logic
{
    static function title(): string;

    static function description(): string;

    public function startGame(Game $game);

    public function roundAction(Round $round, array $options = []);

    public function endRound(Round $round);

    public function playerJoined(Player $player, Game $game);

}
