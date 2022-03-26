<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Move;
use App\Models\Round;
use App\Models\Player;
use App\Models\JustOneGame;
use App\Queue\Events\GameEnded;
use App\Queue\Events\PlayerCreated;
use App\Queue\Events\GameRoundAction;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JustOneTest extends TestCase
{
    use CanSeed;
    use RefreshDatabase;

    /** @test */
    public function join_started_game()
    {
        Event::fake();

        /** @var User $user */
        $user = User::factory()->create();

        /** @var \App\Models\JustOneGame $game */
        $game = JustOneGame::factory()
            ->has(Player::factory()->count(3), 'players')
            ->withHostUser()
            ->withRound()
            ->create();

        $this->actingAs($user)
            ->get(route('justone.join', ['game' => $game->uuid]))
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

        /** @var User $user */
        $user = User::factory()->create();

        /** @var \App\Models\JustOneGame $game */
        $game = JustOneGame::factory()
            ->create(['started_at' => null, 'host_user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('justone.join', ['game' => $game->uuid]))
            ->assertOk()
            ->assertViewIs('GamePage');

        $this->assertDatabaseHas('players', [
            'game_id' => $game->id,
            'user_id' => $user->id,
        ]);

        $player = $game->players()->first();
        $game->refresh();
        $this->assertNotNull($game->started_at);

        $this->assertDatabaseHas('rounds', [
            'game_id'          => $game->id,
            'active_player_id' => $player->id,
        ]);

        Event::assertDispatched(PlayerCreated::class);
        Event::assertDispatched(GameRoundAction::class);
    }

    /** @test */
    public function join_with_existing_player()
    {
        Event::fake();

        /** @var User $user */
        $user = User::factory()->create();

        /** @var \App\Models\JustOneGame $game */
        $game = JustOneGame::factory()
            ->has(Player::factory()->count(3), 'players')
            ->withHostUser()
            ->withRound()
            ->create();

        $player = Player::factory()->create([
            'game_id' => $game->id,
            'user_id' => $user->id,
        ]);

        $this->travel(5);

        $this->actingAs($user)
            ->get(route('justone.join', ['game' => $game->uuid]))
            ->assertOk()
            ->assertViewIs('GamePage');

        $this->assertEmpty(Player::where('game_id', $game->id)->where('created_at', '>', $player->created_at)->get());

        Event::assertNotDispatched(PlayerCreated::class);
        Event::assertNotDispatched(GameRoundAction::class);
    }

    /** @test */
    public function make_empty_move()
    {
        /** @var \App\Models\JustOneGame $game */
        $game = JustOneGame::factory()
            ->has(Player::factory()->count(3), 'players')
            ->withHostUser()
            ->withRound()
            ->create();

        $this->actingAs($game->currentPlayer->user)
            ->post(route('justone.move', ['game' => $game->uuid]))
            ->assertInvalid(['guess', 'clue']);
    }

    /** @test */
    public function make_clue_not_last_move()
    {
        Event::fake();

        /** @var \App\Models\JustOneGame $game */
        $game = JustOneGame::factory()
            ->has(Player::factory()->count(3), 'players')
            ->withHostUser()
            ->withRound()
            ->create();

        /** @var Player $notActivePlayer */
        $notActivePlayer = $game->players->last();

        $this->actingAs($notActivePlayer->user)
            ->post(route('justone.move', ['game' => $game->uuid, 'clue' => 'test']))
            ->assertOk()
            ->assertViewIs('GamePage');

        $game->refresh();
        $this->assertTrue($game->isWaitingForClue);
        $this->assertFalse($game->isWaitingForGuess);
        $this->assertFalse($game->isCompleted);

        /** @var Move $move */
        $move = $notActivePlayer->moves()->latest()->first();
        $this->assertNotNull($notActivePlayer);

        $this->assertEquals('test', $move->payloadAttribute('clue'));
        $this->assertNull($move->score);
        $this->assertNull($game->currentRound->completed_at);

        Event::assertDispatched(GameRoundAction::class);
        Event::assertNotDispatched(GameEnded::class);
    }

    /** @test */
    public function make_clue_last_move()
    {
        Event::fake();

        /** @var \App\Models\JustOneGame $game */
        $game = JustOneGame::factory()
            ->has(Player::factory()->count(4), 'players')
            ->withHostUser()
            ->withRound()
            ->create();

        $activePlayer = $game->players->first();
        /** @var Player $secondPlayer */
        $firstPlayer = $game->players->skip(1)->first();
        /** @var Player $secondPlayer */
        $secondPlayer = $game->players->skip(2)->first();
        /** @var Player $secondPlayer */
        $thirdPlayer = $game->players->skip(3)->first();

        $this->actingAs($firstPlayer->user)
            ->post(route('justone.move', ['game' => $game->uuid, 'clue' => 'subset']));

        $this->actingAs($secondPlayer->user)
            ->post(route('justone.move', ['game' => $game->uuid, 'clue' => 'set']));

        $this->actingAs($thirdPlayer->user)
            ->post(route('justone.move', ['game' => $game->uuid, 'clue' => 'visible']))
            ->assertOk()
            ->assertViewIs('GamePage');

        $game->refresh();

        $this->assertFalse($game->isWaitingForClue);
        $this->assertTrue($game->isWaitingForGuess);
        $this->assertFalse($game->isCompleted);

        /** @var Move $thirdPlayerMove */
        $thirdPlayerMove = $thirdPlayer->moves()->latest()->first();
        $this->assertNotNull($thirdPlayerMove);

        $this->assertEquals('visible', $thirdPlayerMove->payloadAttribute('clue'));
        $this->assertEquals(true, $thirdPlayerMove->payloadAttribute('visible'));
        $this->assertNull($thirdPlayerMove->score);

        Event::assertDispatched(GameRoundAction::class);
        Event::assertNotDispatched(GameEnded::class);
    }

    /** @test */
    public function make_guess()
    {
        /** @var \App\Models\JustOneGame $game */
        $game = JustOneGame::factory()
            ->has(Player::factory()->count(3), 'players')
            ->withHostUser()
            ->withRound('word')
            ->create();

        /** @var Player $activePlayer */
        $activePlayer = $game->players->first();
        /** @var Player $firstPlayer */
        $firstPlayer = $game->players->skip(1)->first();
        /** @var Player $secondPlayer */
        $secondPlayer = $game->players->skip(2)->first();

        $this->actingAs($firstPlayer->user)
            ->post(route('justone.move', ['game' => $game->uuid, 'clue' => 'one']))
            ->assertOk();

        $this->actingAs($secondPlayer->user)
            ->post(route('justone.move', ['game' => $game->uuid, 'clue' => 'two']))
            ->assertOk();

        Event::fake();

        $this->actingAs($activePlayer->user)
            ->post(route('justone.move', ['game' => $game->uuid, 'guess' => 'Word']))
            ->assertOk()
            ->assertViewIs('GamePage');

        $game->refresh();

        $this->assertFalse($game->isWaitingForClue);
        $this->assertFalse($game->isWaitingForGuess);
        $this->assertTrue($game->isCompleted);

        /** @var Move $activePlayerMove */
        $activePlayerMove = $activePlayer->moves()->latest()->first();
        $this->assertNotNull($activePlayerMove);
        $this->assertNotNull($activePlayerMove->score);

        /** @var Move $firstPlayerMove */
        $firstPlayerMove = $activePlayer->moves()->latest()->first();
        $this->assertNotNull($firstPlayerMove);
        $this->assertNotNull($firstPlayerMove->score);

        Event::assertNotDispatched(GameRoundAction::class);
        Event::assertDispatched(GameEnded::class);
    }

    /** @test */
    public function redo_move()
    {
        Event::fake();

        /** @var \App\Models\JustOneGame $game */
        $game = JustOneGame::factory()
            ->has(Player::factory()->count(4), 'players')
            ->withHostUser()
            ->withRound()
            ->create();

        /** @var Player $notActivePlayer */
        $notActivePlayer = $game->players->skip(1)->first();

        $this->actingAs($notActivePlayer->user)
            ->post(route('justone.move', ['game' => $game->uuid, 'clue' => 'test']))
            ->assertOk()
            ->assertViewIs('GamePage');

        /** @var Move $move */
        $move = $notActivePlayer->moves()->latest()->first();
        $this->assertNotNull($move);

        $this->actingAs($notActivePlayer->user)
            ->post(route('justone.move', ['game' => $game->uuid, 'clue' => 'something-else']))
            ->assertOk()
            ->assertViewIs('GamePage');

        $move->refresh();
        $this->assertNull($move->score);
        $this->assertEquals('something-else', $move->payloadAttribute('clue'));
        $this->assertNull($game->currentRound->completed_at);

        $this->assertTrue($game->isWaitingForClue);
        $this->assertFalse($game->isWaitingForGuess);
        $this->assertFalse($game->isCompleted);

        Event::assertDispatched(GameRoundAction::class);
        Event::assertNotDispatched(GameEnded::class);
    }

    /** @test */
    public function end_round()
    {
        Event::fake();

        /** @var \App\Models\JustOneGame $game */
        $game = JustOneGame::factory()
            ->has(Player::factory()->count(3), 'players')
            ->withHostUser()
            ->withRound()
            ->create();

        $firstPlayer = $game->hostPlayer;
        /** @var Player $secondPlayer */
        $oldRound = $game->currentRound;

        $this->assertDatabaseHas(Round::class, [
            'game_id'          => $game->id,
            'active_player_id' => $game->hostPlayer->id,
        ]);

        $this->actingAs($firstPlayer->user)
            ->post(route('justone.round', ['game' => $game->uuid]))
            ->assertOk();

        $this->assertDatabaseHas(Round::class, [
            'game_id'          => $game->id,
            'active_player_id' => $game->nextPlayer->id,
        ]);

        Event::assertDispatched(GameRoundAction::class);
    }
}
