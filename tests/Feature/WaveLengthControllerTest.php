<?php

namespace Tests\Feature;

use App\Models\Game;
use App\Models\Move;
use App\Models\Player;
use App\Models\User;
use App\Models\WaveLengthGame;
use App\Queue\Events\GameEnded;
use App\Queue\Events\GameRoundAction;
use App\Queue\Events\PlayerCreated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class WaveLengthControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function join_started_game()
    {
        Event::fake();

        /** @var User $user */
        $user = User::factory()->create();

        /** @var WaveLengthGame $game */
        $game = WaveLengthGame::factory()
            ->has(Player::factory()->count(2), 'players')
            ->withHostUser()
            ->withRound()
            ->create();

        $this->actingAs($user)
            ->get(route('wavelength.show', ['game' => $game->id]))
            ->assertOk()
            ->assertViewIs('GamePage');

        $this->assertDatabaseHas('players', [
            'game_id' => $game->id,
            'user_id' => $user->id,
        ]);

        Event::assertDispatched(PlayerCreated::class);
    }

    /** @test */
    public function join_new_game()
    {
        Event::fake();

        /** @var Game $game */
        $game = WaveLengthGame::factory()
            ->has(Player::factory()->count(2), 'players')
            ->withHostUser()
            ->create();

        $game->started_at = null;
        $game->save();

        $this->actingAs($game->hostUser)
            ->get(route('wavelength.show', ['game' => $game->id]))
            ->assertOk()
            ->assertViewIs('GamePage');

        $this->assertDatabaseHas('players', [
            'game_id' => $game->id,
            'user_id' => $game->hostUser->id,
        ]);

        $game->refresh();
        $this->assertNotNull($game->started_at);
        $this->assertTrue($game->hostPlayer->is($game->currentPlayer));

        Event::assertDispatched(GameRoundAction::class);
    }

    /** @test */
    public function join_with_existing_player()
    {
        Event::fake();

        /** @var User $user */
        $user = User::factory()->create();

        /** @var Game $game */
        $game = WaveLengthGame::factory()
            ->has(Player::factory()->count(2), 'players')
            ->withHostUser()
            ->withRound()
            ->create();

        $player = Player::factory()->create([
            'game_id' => $game->id,
            'user_id' => $user->id,
        ]);

        $this->travel(5);

        $this->actingAs($user)
            ->get(route('wavelength.show', ['game' => $game->id]))
            ->assertOk()
            ->assertViewIs('GamePage');

        $this->assertEmpty(Player::where('game_id', $game->id)->where('created_at', '>', $player->created_at)->get());

        Event::assertNotDispatched(PlayerCreated::class);
        Event::assertNotDispatched(GameRoundAction::class);
    }

    /** @test */
    public function make_empty_move()
    {
        /** @var Game $game */
        $game = WaveLengthGame::factory()
            ->has(Player::factory()->count(2), 'players')
            ->withHostUser()
            ->withRound()
            ->create();

        $this->actingAs($game->currentPlayer->user)
            ->post(route('wavelength.move', ['game' => $game->id]))
            ->assertForbidden();
    }

    /** @test */
    public function make_clue_move()
    {
        Event::fake();

        /** @var Game $game */
        $game = WaveLengthGame::factory()
            ->has(Player::factory()->count(2), 'players')
            ->withHostUser()
            ->withRound()
            ->create();

        $this->actingAs($game->currentPlayer->user)
            ->post(route('wavelength.move', ['game' => $game->id, 'clue' => 'test']))
            ->assertOk()
            ->assertViewIs('GamePage');

        $game->refresh();

        $this->assertEquals('test', $game->currentRound->payloadAttribute('clue'));
        $this->assertNull($game->currentRound->completed_at);

        $this->assertDatabaseHas('moves', [
            'round_id' => $game->currentRound->id,
            'player_id' => $game->currentPlayer->id,
            'user_id' => $game->currentPlayer->user_id,
        ]);

        /** @var Move $move */
        $move = Move::query()
            ->where('round_id', $game->currentRound->id)
            ->where('player_id', $game->currentPlayer->id)
            ->where('user_id', $game->currentPlayer->user_id)
            ->first();

        $this->assertNull($move->score);

        Event::assertDispatched(GameRoundAction::class);
        Event::assertNotDispatched(GameEnded::class);
    }

    /** @test */
    public function make_guess_not_last_move()
    {
        Event::fake();

        /** @var Game $game */
        $game = WaveLengthGame::factory()
            ->has(Player::factory()->count(2), 'players')
            ->withHostUser()
            ->withRound()
            ->create();

        /** @var Player $notActivePlayer */
        $notActivePlayer = $game->players->skip(1)->first();

        $this->actingAs($notActivePlayer->user)
            ->post(route('wavelength.move', ['game' => $game->id, 'guess' => 23]))
            ->assertOk()
            ->assertViewIs('GamePage');

        $this->assertDatabaseHas('moves', [
            'round_id' => $game->currentRound->id,
            'player_id' => $notActivePlayer->id,
            'user_id' => $notActivePlayer->user_id,
        ]);

        /** @var Move $move */
        $move = Move::query()
            ->where('round_id', $game->currentRound->id)
            ->where('player_id', $notActivePlayer->id)
            ->where('user_id', $notActivePlayer->user_id)
            ->first();
        $this->assertEquals('23', $move->getPayloadWithKey('guess'));
        $this->assertNull($move->score);

        $this->assertNull($game->currentRound->completed_at);

        Event::assertDispatched(GameRoundAction::class);
        Event::assertNotDispatched(GameEnded::class);
    }

    /** @test */
    public function make_guess_last_move()
    {
        Event::fake();

        /** @var Game $game */
        $game = WaveLengthGame::factory()
            ->has(Player::factory(), 'players')
            ->withHostUser()
            ->withRound()
            ->create();

        Move::factory()->state([
            'round_id' => $game->currentRound->id,
            'player_id' => $game->currentPlayer->id,
            'user_id' => $game->currentPlayer->user_id,
        ])->create();

        $hostPlayer = $game->hostPlayer;
        $notHostPlayer = $game->players->filter(fn (Player $player) => ! $player->is($hostPlayer))->first();

        $this->actingAs($hostPlayer->user)
            ->post(route('wavelength.move', ['game' => $game->id, 'clue' => 'twentythree']))
            ->assertOk()
            ->assertViewIs('GamePage');

        Event::assertDispatched(GameRoundAction::class);

        $this->actingAs($notHostPlayer->user)
            ->post(route('wavelength.move', ['game' => $game->id, 'guess' => 23]))
            ->assertOk()
            ->assertViewIs('GamePage');

        $game->refresh();

        $this->assertNotNull($game->currentRound->completed_at);
        $this->assertNotNull($hostPlayer->moves()->latest()->first()->score);
        $this->assertNotNull($notHostPlayer->moves()->latest()->first()->score);

        Event::assertDispatched(GameEnded::class);
    }

    /** @test */
    public function redo_move()
    {
        Event::fake();

        /** @var Game $game */
        $game = WaveLengthGame::factory()
            ->has(Player::factory()->count(2), 'players')
            ->withHostUser()
            ->withRound()
            ->create();

        /** @var Player $notActivePlayer */
        $notActivePlayer = $game->players->skip(1)->first();

        /** @var Move $move */
        $move = Move::factory()->state([
            'round_id' => $game->currentRound->id,
            'player_id' => $notActivePlayer->id,
            'user_id' => $notActivePlayer->user_id,
            'score' => null,
            'payload' => ['guess' => 35],
        ])->create();

        $this->actingAs($notActivePlayer->user)
            ->post(route('wavelength.move', ['game' => $game->id, 'guess' => 87]))
            ->assertOk()
            ->assertViewIs('GamePage');

        $move->refresh();

        $this->assertEquals(87, $move->getPayloadWithKey('guess'));
        $this->assertNull($move->score);

        $this->assertNull($game->currentRound->completed_at);

        Event::assertDispatched(GameRoundAction::class);
        Event::assertNotDispatched(GameEnded::class);
    }

    /** @test */
    public function end_round()
    {
        Event::fake();

        /** @var Game $game */
        $game = WaveLengthGame::factory()
            ->has(Player::factory()->count(2), 'players')
            ->withHostUser()
            ->withRound()
            ->create();
        $oldPlayer = $game->currentPlayer;
        $oldRound = $game->currentRound;

        $this->actingAs($game->currentPlayer->user)
            ->post(route('wavelength.round', ['game' => $game->id]))
            ->assertOk()
            ->assertViewIs('GamePage');

        $game->refresh();

        $this->assertNotEquals($oldPlayer->id, $game->currentPlayer);
        $this->assertNotEquals($oldRound->id, $game->currentRound);

        Event::assertDispatched(GameRoundAction::class);
    }
}
