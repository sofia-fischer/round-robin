<?php

namespace Tests\Feature;

use App\Models\Move;
use App\Models\Player;
use App\Models\Round;
use App\Models\User;
use App\Models\WerewolfGame;
use App\Queue\Events\GameRoundAction;
use App\Queue\Events\PlayerCreated;
use App\ValueObjects\Enums\WerewolfRoleEnum;
use App\ValueObjects\WerewolfBoard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PlanetXController
 */
class PlanetXControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testJoinGame()
    {
        $this->disableExceptionHandling();
        Event::fake();

        /** @var User $user */
        $user = User::factory()->create();

        $game = WerewolfGame::factory()->create();
        $game->startRound();

        $this->actingAs($user)
            ->get(route('werewolf.show', ['game' => $game->id]))
            ->assertOk()
            ->assertViewIs('GamePage');

        $this->assertDatabaseHas('players', [
            'game_id' => $game->id,
            'user_id' => $user->id,
        ]);

        $game->refresh();
        $player = $game->players->firstWhere('user_id', $user->id);
        $board = $game->getCurrentWerewolfBoard();
        $this->assertNull($board->see($player->id));

        Event::assertDispatched(PlayerCreated::class);
    }

    public function testJoinWithExistingPlayer()
    {
        $this->disableExceptionHandling();
        Event::fake();

        /** @var User $user */
        $user = User::factory()->create();

        $game = WerewolfGame::factory()->create();
        $player = Player::factory()->create([
            'game_id' => $game->id,
            'user_id' => $user->id,
        ]);
        $game->startRound();

        $this->travel(5)->minutes();

        $this->actingAs($user)
            ->get(route('werewolf.show', ['game' => $game->id]))
            ->assertOk()
            ->assertViewIs('GamePage');

        $this->assertEmpty(Player::where('game_id', $game->id)->where('created_at', '>', $player->created_at)->get());

        Event::assertNotDispatched(PlayerCreated::class);
    }

    /** @test */
    public function testNextRound()
    {
        $this->disableExceptionHandling();
        Event::fake();

        $game = WerewolfGame::factory()->withHostUser()->create();
        $game->startRound();

        $currentRoundId = $game->currentRound->id;

        $this->travel(5)->minutes();

        $this->actingAs($game->hostUser)
            ->post(route('werewolf.round', ['game' => $game->id]))
            ->assertOk()
            ->assertViewIs('GamePage');

        $game->refresh();
        $this->assertNotSame($currentRoundId, $game->currentRound->id);

        Event::assertDispatched(GameRoundAction::class);
    }

    /** @test */
    public function testSeeMove()
    {
        $game = WerewolfGame::factory()->withHostUser()->create();

        $seer = $game->hostPlayer;
        $targetPlayer = $game->players()->create(['user_id' => User::factory()->create()->id]);

        $board = new WerewolfBoard([
            $seer->id => WerewolfRoleEnum::SEER,
            $targetPlayer->id => WerewolfRoleEnum::VILLAGER,
        ]);
        Round::create(['game_id' => $game->id, 'payload' => $board->toArray()]);

        $this->actingAs($seer->user)
            ->post(route('werewolf.move', ['game' => $game->id, 'see' => $targetPlayer->id]))
            ->assertOk();

        $this->assertDatabaseHas('moves', [
            'round_id' => $game->currentRound->id,
            'player_id' => $seer->id,
            'user_id' => $seer->user_id,
        ]);

        $move = Move::query()
            ->where('round_id', $game->currentRound->id)
            ->where('player_id', $seer->id)
            ->where('user_id', $seer->user_id)
            ->first();

        $this->assertSame($targetPlayer->id, $move->getPayloadWithKey('move')['see'] ?? false);

        $this->actingAs($game->hostUser)
            ->post(route('werewolf.sunrise', ['game' => $game->id]))
            ->assertOk();

        $game->refresh();
        $board = $game->getCurrentWerewolfBoard();

        self::assertTrue($board->isDay());
        self::assertTrue($board->canSee($seer->id, $targetPlayer->id));
    }

    public function testMakeNonSeerMove()
    {
        Event::fake();

        $game = WerewolfGame::factory()->withHostUser()->create();

        $seer = $game->hostPlayer;
        $targetPlayer = $game->players()->create(['user_id' => User::factory()->create()->id]);

        $board = new WerewolfBoard([
            $seer->id => WerewolfRoleEnum::WEREWOLF,
            $targetPlayer->id => WerewolfRoleEnum::VILLAGER,
        ]);
        Round::create(['game_id' => $game->id, 'payload' => $board->toArray()]);

        $this->actingAs($seer->user)
            ->post(route('werewolf.move', ['game' => $game->id, 'see' => $targetPlayer->id]))
            ->assertForbidden();
    }

    public function testVote()
    {
        $this->disableExceptionHandling();
        $game = WerewolfGame::factory()->withHostUser()->create();

        $player = $game->hostPlayer;
        $targetPlayer = $game->players()->create(['user_id' => User::factory()->create()->id]);

        $board = new WerewolfBoard([
            $player->id => WerewolfRoleEnum::WEREWOLF,
            $targetPlayer->id => WerewolfRoleEnum::VILLAGER,
        ]);
        Round::create(['game_id' => $game->id, 'payload' => $board->toArray()]);

        // make sunrise
        $this->actingAs($game->hostUser)
            ->post(route('werewolf.vote', ['game' => $game->id, 'vote' => $targetPlayer->id]))
            ->assertOk();

        $move = Move::query()
            ->where('round_id', $game->currentRound->id)
            ->where('player_id', $player->id)
            ->where('user_id', $player->user_id)
            ->first();

        $this->assertSame($targetPlayer->id, $move->getPayloadWithKey('vote'));
    }
}
