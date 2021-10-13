<?php

namespace App\Queue\Events;

use App\Models\Game;
use Illuminate\Broadcasting\Channel;

/**
 * Class GameStarted
 *
 * @package App\Queue\Events
 */
class GameStarted extends BaseEvent
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
