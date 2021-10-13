<?php

namespace App\Jobs;

use App\Models\Game;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Support\GameLogics\OneNightWerewolfLogic;

class OneNightWerewolfNightJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $gameId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($gameId)
    {
        $this->gameId = $gameId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /** @var Game $game */
        $game = Game::findOrFail($this->gameId);

        OneNightWerewolfLogic::calculateSunrise($game->currentRound);
    }
}
