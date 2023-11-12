<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\User;
use Tests\TestCase;

class GameRelationTest extends TestCase
{
    public function testAuthenticatedPlayerMove()
    {
        /** @var Game $game */
        $game = Game::factory()->create();

        /** @var \App\Models\Player $player */
        $player = $game->players()->create([
            'user_id' => User::factory()->create()->id,
        ]);

        /** @var \App\Models\Round $round */
        $round = $game->rounds()->create([
            'active_player_id' => 1,
        ]);

        $move = $player->moves()->create([
            'round_id' => $round->id,
            'user_id' => $player->user_id,
        ]);

        $this->actingAs($player->user);
        $this->assertEquals($move->id, $game->authenticatedPlayerMove->id);
        $this->assertEquals($move->id, $round->authenticatedPlayerMove->id);
    }
}
