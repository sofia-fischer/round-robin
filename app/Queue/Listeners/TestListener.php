<?php

namespace App\Queue\Listeners;

use App\Queue\Events\TestEvent;
use Illuminate\Contracts\Queue\ShouldQueue;

class TestListener implements ShouldQueue
{
    public $connection = 'database';

    public $queue = 'game-queue';

    public function handle(TestEvent $event)
    {
        $event->game->update(['token' => 'Tested by Event']);
    }

    public function shouldQueue(TestEvent $event)
    {
        return true;
    }
}
