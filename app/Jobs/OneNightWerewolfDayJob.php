<?php

namespace App\Jobs;

use App\Models\Game;
use App\Support\GamePolicies\OneNightWerewolfPolicy;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OneNightWerewolfDayJob implements ShouldQueue
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
        Log::info('OneNightWerewolfNightJob started');
        /** @var Game $game */
        $game = Game::findOrFail($this->gameId);

        OneNightWerewolfPolicy::calculateResults($game->currentRound);
    }
}
