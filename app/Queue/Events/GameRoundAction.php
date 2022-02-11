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
    public function __construct(public Game $game)
    {
    }

    public function broadcastOn()
    {
        return new Channel('Game.' . $this->game->uuid);
    }
}
