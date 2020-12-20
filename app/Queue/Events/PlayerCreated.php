<?php

namespace App\Queue\Events;

use App\Models\Player;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class PlayerCreated
 *
 * @package App\Queue\Events
 */
class PlayerCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $player_id;

    public function __construct($player)
    {
        $this->player_id = $player;
    }

    public function broadcastOn()
    {
        $player = Player::find($this->player_id);

        if (!$player) {
            return;
        }

        return new Channel('lol');
    }
}
