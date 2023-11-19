<?php

declare(strict_types=1);

namespace App\Queue\Events;

use App\Models\Player;
use Illuminate\Broadcasting\Channel;

class PlayerCreated extends BaseEvent
{
    public function __construct(public Player $player)
    {
    }

    public function broadcastOn()
    {
        return new Channel('Game.' . $this->player->game->id);
    }
}
