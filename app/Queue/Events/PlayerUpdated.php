<?php

declare(strict_types=1);

namespace App\Queue\Events;

use App\Models\Player;
use Illuminate\Broadcasting\Channel;

/**
 * Class PlayerUpdated
 *
 * @package App\Queue\Events
 */
class PlayerUpdated extends BaseEvent
{
    public Player $player;

    public function __construct($player_id)
    {
        $this->player = Player::find($player_id);
    }

    public function broadcastOn()
    {
        return new Channel('Game.' . $this->player->game->id);
    }
}
