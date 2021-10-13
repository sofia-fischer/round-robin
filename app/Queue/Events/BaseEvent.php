<?php

namespace App\Queue\Events;

use App\Models\Game;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class BaseEvent
 * @package App\Queue\Events
 */
abstract class BaseEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
}
