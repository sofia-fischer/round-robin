<?php

namespace App\Queue\Events;

use App\Models\Game;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class GameStarted
 *
 * @package App\Queue\Events
 */
class GameStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

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
