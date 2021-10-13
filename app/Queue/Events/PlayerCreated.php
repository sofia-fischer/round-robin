<?php

namespace App\Queue\Events;

use App\Models\Player;
use Illuminate\Broadcasting\Channel;

class PlayerCreated extends BaseEvent
{
    public Player $player;

    public function __construct($player_id)
    {
        $this->player = Player::find($player_id);
    }

    public function broadcastOn()
    {
        return new Channel('Game.' . $this->player->game->uuid);
    }
}
