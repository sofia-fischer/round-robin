<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\Round;
use App\Models\Player;

trait CanSeed
{
    public function startedWavelengthGame(): Game
    {
        /** @var Game $game */
        $game = Game::factory()
            ->wavelength()
            ->started()
            ->has(Player::factory()->count(3), 'players')
            ->create();

        /** @var Player $activePlayer */
        $activePlayer = $game->players->first();

        $game->host_user_id = $activePlayer->user_id;
        $game->save();

        /** @var Round $round */
        $round = Round::factory()->create([
            'game_id'          => $game->id,
            'active_player_id' => $activePlayer->id,
            'payload'          => [
                'waveLength' => random_int(0, 100),
                'antonym1'   => 'antonym1',
                'antonym2'   => 'antonym2',
            ],
        ]);

        return $game;
    }
}
