<?php

namespace App\Queue\Events;

use App\Models\Game;
use Illuminate\Broadcasting\Channel;

/**
 * Class GameRoundAction
 *
 * @package App\Queue\Events
 */
class GameRoundAction extends BaseEvent
{
    public $gameId;

    public function __construct($gameId)
    {
        $this->gameId = $gameId;
    }

    public function broadcastOn()
    {
        $game = Game::findOrFail($this->gameId);

        return new Channel('Game.' . $game->uuid);
    }
}
