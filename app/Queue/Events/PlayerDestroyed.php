<?php

namespace App\Queue\Events;

use App\Models\Player;
use Illuminate\Broadcasting\Channel;

class PlayerDestroyed extends BaseEvent
{
    public Player $player;

    public function __construct($player_id)
    {
        $this->player = Player::withTrashed()->find($player_id);
    }

    public function broadcastOn()
    {
        return new Channel('Game.' . $this->player->game->uuid);
    }
}
