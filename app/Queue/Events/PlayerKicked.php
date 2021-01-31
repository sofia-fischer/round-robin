<?php

namespace App\Queue\Events;

use App\Models\Group;
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
class PlayerKicked implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $player_id;

    public function __construct($player)
    {
        $this->player_id = $player;
    }

    public function broadcastOn()
    {
        $group = Group::query()
            ->whereHas('players', function ($players) {
                $players->withTrashed()->where('id', $this->player_id);
            })
            ->firstOrFail();

        return new Channel('Group.' . $group->uuid);
    }
}
