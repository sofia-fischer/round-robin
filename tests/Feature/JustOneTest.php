<?php

namespace Tests\Feature;

use App\Models\JustOneGame;
use App\Models\Move;
use App\Models\Player;
use App\Models\Round;
use App\Models\User;
use App\Queue\Events\GameEnded;
use App\Queue\Events\GameRoundAction;
use App\Queue\Events\PlayerCreated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class JustOneTest extends TestCase
{
    use RefreshDatabase;

    public function testJoinStartedGame()
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
            ->get(route('justone.show', ['game' => $game->id]))
            ->assertOk()
            ->assertViewIs('GamePage');

        $this->assertDatabaseHas('players', [
            'game_id' => $game->id,
            'user_id' => $user->id,
        ]);

        Event::assertDispatched(PlayerCreated::class);
    }

    public function testJoinNewGame()
    {
        Event::fake();

        /** @var User $user */
        $user = User::factory()->create();

        /** @var \App\Models\JustOneGame $game */
        $game = JustOneGame::factory()
            ->create(['started_at' => null, 'host_user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('justone.show', ['game' => $game->id]))
            ->assertRedirect(route('justone.show', ['game' => $game->id]));

        $this->assertDatabaseHas('players', [
            'game_id' => $game->id,
            'user_id' => $user->id,
        ]);

        $player = $game->players()->first();
        $game->refresh();
        $this->assertNotNull($game->started_at);

        $this->assertDatabaseHas('rounds', [
            'game_id' => $game->id,
            'active_player_id' => $player->id,
        ]);

        Event::assertDispatched(PlayerCreated::class);
        Event::assertDispatched(GameRoundAction::class);
    }

    public function testJoinWithExistingPlayer()
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
            ->get(route('justone.show', ['game' => $game->id]))
            ->assertOk()
            ->assertViewIs('GamePage');

        $this->assertEmpty(Player::where('game_id', $game->id)->where('created_at', '>', $player->created_at)->get());

        Event::assertNotDispatched(PlayerCreated::class);
        Event::assertNotDispatched(GameRoundAction::class);
    }

    public function testMakeEmptyMove()
    {
        /** @var \App\Models\JustOneGame $game */
        $game = JustOneGame::factory()
            ->has(Player::factory()->count(3), 'players')
            ->withHostUser()
            ->withRound()
            ->create();

        $this->actingAs($game->currentPlayer->user)
            ->post(route('justone.move', ['game' => $game->id]))
            ->assertForbidden();
    }

    public function testMakeClueNotLastMove()
    {
        Event::fake();

        /** @var \App\Models\JustOneGame $game */
        $game = JustOneGame::factory()
            ->withHostUser()
            ->withRound()
            ->has(Player::factory()->count(2), 'players')
            ->create();

        /** @var Player $notActivePlayer */
        $notActivePlayer = $game->players->first();

        $this->actingAs($notActivePlayer->user)
            ->post(route('justone.move', ['game' => $game->id, 'clue' => 'test']))
            ->assertRedirect(route('justone.show', ['game' => $game->id]));

        $game->refresh();
        $this->assertTrue($game->isWaitingForClue);
        $this->assertFalse($game->isWaitingForGuess);
        $this->assertFalse($game->isCompleted);

        /** @var Move $move */
        $move = $notActivePlayer->moves()->latest()->first();
        $this->assertNotNull($notActivePlayer);

        $this->assertEquals('test', $move->getPayloadWithKey('clue'));
        $this->assertNull($move->score);
        $this->assertNull($game->currentRound->completed_at);

        Event::assertDispatched(GameRoundAction::class);
        Event::assertNotDispatched(GameEnded::class);
    }

    public function testMakeVisibleAndInvisibleClues()
    {
        Event::fake();

        /** @var \App\Models\JustOneGame $game */
        $game = JustOneGame::factory()
            ->has(Player::factory()->count(3), 'players')
            ->withHostUser()
            ->withRound('word')
            ->create();

        /** @var Player $firstPlayer */
        $firstPlayer = $game->players->first();
        /** @var Player $secondPlayer */
        $secondPlayer = $game->players->skip(1)->first();
        /** @var Player $thirdPlayer */
        $thirdPlayer = $game->players->skip(2)->first();

        $this->actingAs($firstPlayer->user)
            ->post(route('justone.move', ['game' => $game->id, 'clue' => 'subset']))
            ->assertRedirect(route('justone.show', ['game' => $game->id]));
        $this->actingAs($secondPlayer->user)
            ->post(route('justone.move', ['game' => $game->id, 'clue' => 'set']))
            ->assertRedirect(route('justone.show', ['game' => $game->id]));
        $this->actingAs($thirdPlayer->user)
            ->post(route('justone.move', ['game' => $game->id, 'clue' => 'visible']))
            ->assertRedirect(route('justone.show', ['game' => $game->id]));
        $game->refresh();

        $this->assertFalse($game->isWaitingForClue);
        $this->assertTrue($game->isWaitingForGuess);
        $this->assertFalse($game->isCompleted);

        /** @var Move $thirdPlayerMove */
        $thirdPlayerMove = $thirdPlayer->moves()->latest()->first();
        $this->assertNotNull($thirdPlayerMove);

        $this->assertEquals('visible', $thirdPlayerMove->getPayloadWithKey('clue'));
        $this->assertEquals(true, $thirdPlayerMove->getPayloadWithKey('visible'));
        $this->assertNull($thirdPlayerMove->score);

        /** @var Move $secondPlayer */
        $secondPlayer = $secondPlayer->moves()->latest()->first();
        $this->assertNotNull($secondPlayer);

        $this->assertEquals('set', $secondPlayer->getPayloadWithKey('clue'));
        $this->assertEquals(false, $secondPlayer->getPayloadWithKey('visible'));
        $this->assertNull($secondPlayer->score);

        Event::assertDispatched(GameRoundAction::class);
        Event::assertNotDispatched(GameEnded::class);
    }

    public function testMakeGuess()
    {
        /** @var \App\Models\JustOneGame $game */
        $game = JustOneGame::factory()
            ->has(Player::factory()->count(2), 'players')
            ->withHostUser()
            ->withRound('word')
            ->create();

        /** @var Player $activePlayer */
        $activePlayer = $game->players->last();
        /** @var Player $firstPlayer */
        $firstPlayer = $game->players->first();
        /** @var Player $secondPlayer */
        $secondPlayer = $game->players->skip(1)->first();

        $this->actingAs($firstPlayer->user)
            ->post(route('justone.move', ['game' => $game->id, 'clue' => 'one']))
            ->assertRedirect(route('justone.show', ['game' => $game->id]));

        $this->actingAs($secondPlayer->user)
            ->post(route('justone.move', ['game' => $game->id, 'clue' => 'two']))
            ->assertRedirect(route('justone.show', ['game' => $game->id]));

        Event::fake();

        $this->actingAs($activePlayer->user)
            ->post(route('justone.move', ['game' => $game->id, 'guess' => 'Word']))
            ->assertRedirect(route('justone.show', ['game' => $game->id]));

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

    public function testRedoMove()
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
            ->post(route('justone.move', ['game' => $game->id, 'clue' => 'test']))
            ->assertRedirect(route('justone.show', ['game' => $game->id]));


        /** @var Move $move */
        $move = $notActivePlayer->moves()->latest()->first();
        $this->assertNotNull($move);

        $this->actingAs($notActivePlayer->user)
            ->post(route('justone.move', ['game' => $game->id, 'clue' => 'something-else']))
            ->assertRedirect(route('justone.show', ['game' => $game->id]));

        $move->refresh();
        $this->assertNull($move->score);
        $this->assertEquals('something-else', $move->getPayloadWithKey('clue'));
        $this->assertNull($game->currentRound->completed_at);

        $this->assertTrue($game->isWaitingForClue);
        $this->assertFalse($game->isWaitingForGuess);
        $this->assertFalse($game->isCompleted);

        Event::assertDispatched(GameRoundAction::class);
        Event::assertNotDispatched(GameEnded::class);
    }

    public function testEndRound()
    {
        Event::fake();
        $this->disableExceptionHandling();

        /** @var \App\Models\JustOneGame $game */
        $game = JustOneGame::factory()
            ->has(Player::factory()->count(2), 'players')
            ->withHostUser()
            ->withRound()
            ->create();

        // needed to show the game view
        $this->actingAs($game->hostPlayer->user)
            ->post(route('justone.move', ['game' => $game->id, 'guess' => 'debug']))
            ->assertRedirect(route('justone.show', ['game' => $game->id]));

        $this->assertDatabaseHas(Round::class, [
            'game_id' => $game->id,
            'active_player_id' => $game->hostPlayer->id,
        ]);

        $this->actingAs($game->hostPlayer->user)
            ->post(route('justone.round', ['game' => $game->id]))
            ->assertRedirect(route('justone.show', ['game' => $game->id]));

        $this->assertDatabaseHas(Round::class, [
            'game_id' => $game->id,
            'active_player_id' => $game->hostPlayer->id,
        ]);

        $this->assertDatabaseHas(Round::class, [
            'game_id' => $game->id,
            'active_player_id' => $game->nextPlayer->id,
        ]);

        Event::assertDispatched(GameRoundAction::class);
    }
}
