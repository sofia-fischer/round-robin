<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Move;
use App\Models\Player;
use App\Models\WerewolfGame;
use App\Queue\Events\PlayerCreated;
use Illuminate\Support\Facades\Event;
use App\Queue\Events\GameRoundAction;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WerewolfTest extends TestCase
{
    use CanSeed;
    use RefreshDatabase;

    /** @test */
    public function join_started_game()
    {
        $this->disableExceptionHandling();

        Event::fake();

        /** @var User $user */
        $user = User::factory()->create();

        /** @var \App\Models\WerewolfGame $game */
        $game = WerewolfGame::factory()->startedWithAllRoles()->create();

        $this->actingAs($user)
            ->get(route('werewolf.show', ['game' => $game->uuid]))
            ->assertOk()
            ->assertViewIs('GamePage');

        $this->assertDatabaseHas('players', [
            'game_id' => $game->id,
            'user_id' => $user->id,
        ]);

        $game->refresh();
        $player = $game->players->firstWhere('user_id', $user->id);
        self::assertSame($game->playerRoles->get($player->id), WerewolfGame::WATCHER);

        Event::assertDispatched(PlayerCreated::class);
    }

    /** @test */
    public function join_with_existing_player()
    {
        $this->disableExceptionHandling();

        Event::fake();

        /** @var User $user */
        $user = User::factory()->create();

        /** @var \App\Models\WerewolfGame $game */
        $game   = WerewolfGame::factory()->startedWithAllRoles()->create();
        $player = Player::factory()->create([
            'game_id' => $game->id,
            'user_id' => $user->id,
        ]);

        $this->travel(5)->minutes();

        $this->actingAs($user)
            ->get(route('werewolf.show', ['game' => $game->uuid]))
            ->assertOk()
            ->assertViewIs('GamePage');

        $this->assertEmpty(Player::where('game_id', $game->id)->where('created_at', '>', $player->created_at)->get());

        Event::assertNotDispatched(PlayerCreated::class);
    }

    /** @test */
    public function start_game()
    {
        $this->disableExceptionHandling();

        Event::fake();

        /** @var \App\Models\WerewolfGame $game */
        $game           = WerewolfGame::factory()->startedWithAllRoles()->create();
        $currentRoundId = $game->currentRound->id;

        $this->travel(5)->minutes();

        $this->actingAs($game->hostUser)
            ->post(route('werewolf.round', ['game' => $game->uuid]))
            ->assertOk()
            ->assertViewIs('GamePage');

        $game->refresh();
        $this->assertNotSame($currentRoundId, $game->currentRound->id);
        $this->assertSame($game->currentPayloadAttribute('state'), 'night');
        $this->assertCount($game->players->count(), $game->playerRoles);
        $this->assertCount(3, $game->extraRoles);
        $this->assertContains(WerewolfGame::WEREWOLF, $game->playerRoles);

        Event::assertDispatched(GameRoundAction::class);
    }

    /** @test */
    public function make_werewolf_move()
    {
        /** @var \App\Models\WerewolfGame $game */
        $game         = WerewolfGame::factory()->startedWithAllRoles()->create();
        $targetPlayer = $game->players->random()->id;
        $extraCard    = WerewolfGame::$leftAnonymRole;
        /** @var Player $player */
        $player = $game->playerWithRole(WerewolfGame::WEREWOLF)->first();

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'see' => $targetPlayer]))
            ->assertInvalid(['see']);

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'steal' => $targetPlayer]))
            ->assertInvalid(['see']);

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'switch1' => $targetPlayer]))
            ->assertInvalid(['see']);

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'switch2' => $targetPlayer]))
            ->assertInvalid(['see']);

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'drunk' => $extraCard]))
            ->assertInvalid(['see']);

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'vote' => $extraCard]))
            ->assertInvalid(['vote']);

        $this->assertDatabaseMissing('moves', [
            'round_id'  => $game->currentRound->id,
            'player_id' => $player->id,
            'user_id'   => $player->user_id,
        ]);

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'see' => $extraCard]))
            ->assertOk();

        $this->assertDatabaseHas('moves', [
            'round_id'  => $game->currentRound->id,
            'player_id' => $player->id,
            'user_id'   => $player->user_id,
        ]);

        $move = Move::query()
            ->where('round_id', $game->currentRound->id)
            ->where('player_id', $player->id)
            ->where('user_id', $player->user_id)
            ->first();

        $this->assertSame($extraCard, $move->getPayloadWithKey('see'));

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'vote' => $targetPlayer]))
            ->assertOk();

        $this->assertSame($targetPlayer, $move->fresh()->getPayloadWithKey('vote'));

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'vote' => 'nobody']))
            ->assertOk();

        $this->assertSame('nobody', $move->fresh()->getPayloadWithKey('vote'));
    }

    /** @test */
    public function make_seer_move()
    {
        /** @var \App\Models\WerewolfGame $game */
        $game         = WerewolfGame::factory()->startedWithAllRoles()->create();
        $targetPlayer = $game->players->random()->id;
        $extraCard    = WerewolfGame::$leftAnonymRole;
        /** @var Player $player */
        $player = $game->playerWithRole(WerewolfGame::SEER)->first();

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'steal' => $targetPlayer]))
            ->assertInvalid(['see']);

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'switch1' => $targetPlayer]))
            ->assertInvalid(['see']);

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'switch2' => $targetPlayer]))
            ->assertInvalid(['see']);

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'drunk' => $extraCard]))
            ->assertInvalid(['see']);

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'vote' => $extraCard]))
            ->assertInvalid(['vote']);

        $this->assertDatabaseMissing('moves', [
            'round_id'  => $game->currentRound->id,
            'player_id' => $player->id,
            'user_id'   => $player->user_id,
        ]);

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'see' => $extraCard]))
            ->assertOk();

        $this->assertDatabaseHas('moves', [
            'round_id'  => $game->currentRound->id,
            'player_id' => $player->id,
            'user_id'   => $player->user_id,
        ]);

        $move = Move::query()
            ->where('round_id', $game->currentRound->id)
            ->where('player_id', $player->id)
            ->where('user_id', $player->user_id)
            ->first();

        $this->assertSame($extraCard, $move->getPayloadWithKey('see'));

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'see' => $targetPlayer]))
            ->assertOk();

        $this->assertSame($targetPlayer, $move->fresh()->getPayloadWithKey('see'));

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'vote' => $targetPlayer]))
            ->assertOk();

        $this->assertSame($targetPlayer, $move->fresh()->getPayloadWithKey('vote'));

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'vote' => 'nobody']))
            ->assertOk();

        $this->assertSame('nobody', $move->fresh()->getPayloadWithKey('vote'));
    }

    /** @test */
    public function make_robber_move()
    {
        /** @var \App\Models\WerewolfGame $game */
        $game         = WerewolfGame::factory()->startedWithAllRoles()->create();
        $targetPlayer = $game->players->random()->id;
        $extraCard    = WerewolfGame::$leftAnonymRole;
        /** @var Player $player */
        $player = $game->playerWithRole(WerewolfGame::ROBBER)->first();

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'see' => $targetPlayer]))
            ->assertInvalid(['steal']);

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'steal' => $extraCard]))
            ->assertInvalid(['steal']);

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'switch1' => $targetPlayer]))
            ->assertInvalid(['steal']);

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'switch2' => $targetPlayer]))
            ->assertInvalid(['steal']);

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'drunk' => $extraCard]))
            ->assertInvalid(['steal']);

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'vote' => $extraCard]))
            ->assertInvalid(['vote']);

        $this->assertDatabaseMissing('moves', [
            'round_id'  => $game->currentRound->id,
            'player_id' => $player->id,
            'user_id'   => $player->user_id,
        ]);

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'steal' => $targetPlayer]))
            ->assertOk();

        $this->assertDatabaseHas('moves', [
            'round_id'  => $game->currentRound->id,
            'player_id' => $player->id,
            'user_id'   => $player->user_id,
        ]);

        $move = Move::query()
            ->where('round_id', $game->currentRound->id)
            ->where('player_id', $player->id)
            ->where('user_id', $player->user_id)
            ->first();

        $this->assertSame($targetPlayer, $move->getPayloadWithKey('steal'));

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'vote' => $targetPlayer]))
            ->assertOk();

        $this->assertSame($targetPlayer, $move->fresh()->getPayloadWithKey('vote'));

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'vote' => 'nobody']))
            ->assertOk();

        $this->assertSame('nobody', $move->fresh()->getPayloadWithKey('vote'));
    }

    /** @test */
    public function make_troublemaker_move()
    {
        /** @var \App\Models\WerewolfGame $game */
        $game         = WerewolfGame::factory()->startedWithAllRoles()->create();
        $targetPlayer = $game->players->random()->id;
        $extraCard    = WerewolfGame::$leftAnonymRole;
        /** @var Player $player */
        $player = $game->playerWithRole(WerewolfGame::TROUBLEMAKER)->first();

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'see' => $targetPlayer]))
            ->assertInvalid(['switch1']);

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'steal' => $targetPlayer]))
            ->assertInvalid(['switch1']);

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'drunk' => $extraCard]))
            ->assertInvalid(['switch1']);

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'vote' => $extraCard]))
            ->assertInvalid(['vote']);

        $this->assertDatabaseMissing('moves', [
            'round_id'  => $game->currentRound->id,
            'player_id' => $player->id,
            'user_id'   => $player->user_id,
        ]);

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'switch1' => $targetPlayer]))
            ->assertOk();

        $this->assertDatabaseHas('moves', [
            'round_id'  => $game->currentRound->id,
            'player_id' => $player->id,
            'user_id'   => $player->user_id,
        ]);

        $move = Move::query()
            ->where('round_id', $game->currentRound->id)
            ->where('player_id', $player->id)
            ->where('user_id', $player->user_id)
            ->first();

        $this->assertSame($targetPlayer, $move->getPayloadWithKey('switch1'));

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'switch2' => $extraCard]))
            ->assertOk();

        $this->assertSame($extraCard, $move->fresh()->getPayloadWithKey('switch2'));

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'vote' => $targetPlayer]))
            ->assertOk();

        $this->assertSame($targetPlayer, $move->fresh()->getPayloadWithKey('vote'));

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'vote' => 'nobody']))
            ->assertOk();

        $this->assertSame('nobody', $move->fresh()->getPayloadWithKey('vote'));
    }

    /** @test */
    public function make_drunk_move()
    {
        /** @var \App\Models\WerewolfGame $game */
        $game         = WerewolfGame::factory()->startedWithAllRoles()->create();
        $targetPlayer = $game->players->random()->id;
        $extraCard    = WerewolfGame::$leftAnonymRole;
        /** @var Player $player */
        $player = $game->playerWithRole(WerewolfGame::DRUNK)->first();

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'see' => $targetPlayer]))
            ->assertInvalid(['drunk']);

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'steal' => $targetPlayer]))
            ->assertInvalid(['drunk']);

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'switch1' => $targetPlayer]))
            ->assertInvalid(['drunk']);

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'switch2' => $targetPlayer]))
            ->assertInvalid(['drunk']);

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'drunk' => $targetPlayer]))
            ->assertInvalid(['drunk']);

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'vote' => $extraCard]))
            ->assertInvalid(['vote']);

        $this->assertDatabaseMissing('moves', [
            'round_id'  => $game->currentRound->id,
            'player_id' => $player->id,
            'user_id'   => $player->user_id,
        ]);

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'drunk' => $extraCard]))
            ->assertOk();

        $this->assertDatabaseHas('moves', [
            'round_id'  => $game->currentRound->id,
            'player_id' => $player->id,
            'user_id'   => $player->user_id,
        ]);

        $move = Move::query()
            ->where('round_id', $game->currentRound->id)
            ->where('player_id', $player->id)
            ->where('user_id', $player->user_id)
            ->first();

        $this->assertSame($extraCard, $move->getPayloadWithKey('drunk'));

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'vote' => $targetPlayer]))
            ->assertOk();

        $this->assertSame($targetPlayer, $move->fresh()->getPayloadWithKey('vote'));

        $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'vote' => 'nobody']))
            ->assertOk();

        $this->assertSame('nobody', $move->fresh()->getPayloadWithKey('vote'));
    }

    /** @test */
    public function make_villager_or_other_move()
    {
        /** @var \App\Models\WerewolfGame $game */
        $game         = WerewolfGame::factory()->startedWithAllRoles()->create();
        $targetPlayer = $game->players->random()->id;
        $extraCard    = WerewolfGame::$leftAnonymRole;
        /** @var Player $player */
        $player1 = $game->playerWithRole(WerewolfGame::MASON)->first();
        $player2 = $game->playerWithRole(WerewolfGame::MINION)->first();
        $player3 = $game->playerWithRole(WerewolfGame::VILLAGER)->first();
        $player4 = $game->playerWithRole(WerewolfGame::TANNER)->first();
        $player5 = $game->playerWithRole(WerewolfGame::INSOMNIAC)->first();

        collect([$player1, $player2, $player3, $player4, $player5,])
            ->each(function (Player $player) use ($targetPlayer, $extraCard, $game) {
                $this->actingAs($player->user)
                    ->post(route('werewolf.move', ['game' => $game->uuid, 'see' => $extraCard]))
                    ->assertInvalid(['vote']);

                $this->actingAs($player->user)
                    ->post(route('werewolf.move', ['game' => $game->uuid, 'steal' => $targetPlayer]))
                    ->assertInvalid(['vote']);

                $this->actingAs($player->user)
                    ->post(route('werewolf.move', ['game' => $game->uuid, 'switch1' => $targetPlayer]))
                    ->assertInvalid(['vote']);

                $this->actingAs($player->user)
                    ->post(route('werewolf.move', ['game' => $game->uuid, 'switch2' => $targetPlayer]))
                    ->assertInvalid(['vote']);

                $this->actingAs($player->user)
                    ->post(route('werewolf.move', ['game' => $game->uuid, 'drunk' => $extraCard]))
                    ->assertInvalid(['vote']);

                $this->actingAs($player->user)
                    ->post(route('werewolf.move', ['game' => $game->uuid, 'vote' => $extraCard]))
                    ->assertInvalid(['vote']);

                $this->assertDatabaseMissing('moves', [
                    'round_id'  => $game->currentRound->id,
                    'player_id' => $player->id,
                    'user_id'   => $player->user_id,
                ]);

                $this->actingAs($player->user)
                    ->post(route('werewolf.move', ['game' => $game->uuid, 'vote' => $targetPlayer]))
                    ->assertOk();

                $this->assertDatabaseHas('moves', [
                    'round_id'  => $game->currentRound->id,
                    'player_id' => $player->id,
                    'user_id'   => $player->user_id,
                ]);

                $move = Move::query()
                    ->where('round_id', $game->currentRound->id)
                    ->where('player_id', $player->id)
                    ->where('user_id', $player->user_id)
                    ->first();

                $this->assertSame($targetPlayer, $move->fresh()->getPayloadWithKey('vote'));

                $this->actingAs($player->user)
                    ->post(route('werewolf.move', ['game' => $game->uuid, 'vote' => 'nobody']))
                    ->assertOk();

                $this->assertSame('nobody', $move->fresh()->getPayloadWithKey('vote'));
            });
    }

    /** @test */
    public function sunrise()
    {
        /** @var \App\Models\WerewolfGame $game */
        $game = WerewolfGame::factory()->startedWithAllRoles()->create();

        // werewolf sees left card
        /** @var Player $werewolf */
        $werewolf = $game->playerWithRole(WerewolfGame::WEREWOLF)->first();
        $this->actingAs($werewolf->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'see' => WerewolfGame::$leftAnonymRole]))
            ->assertOk();

        // seer sees werewolf
        /** @var Player $seer */
        $seer = $game->playerWithRole(WerewolfGame::SEER)->first();
        $this->actingAs($seer->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'see' => $werewolf->id]))
            ->assertOk();

        // robber robs werewolf
        /** @var Player $robber */
        $robber = $game->playerWithRole(WerewolfGame::ROBBER)->first();
        $this->actingAs($robber->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'steal' => $werewolf->id]))
            ->assertOk();

        // troublemaker switches robber and insomniac
        /** @var Player $troublemaker */
        $troublemaker = $game->playerWithRole(WerewolfGame::TROUBLEMAKER)->first();
        /** @var Player $insomniac */
        $insomniac = $game->playerWithRole(WerewolfGame::INSOMNIAC)->first();
        $this->actingAs($troublemaker->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'switch1' => $robber->id]))
            ->assertOk();
        $this->actingAs($troublemaker->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'switch2' => $insomniac->id]))
            ->assertOk();

        // drunk takes left card
        /** @var Player $drunk */
        $drunk = $game->playerWithRole(WerewolfGame::DRUNK)->first();
        $this->actingAs($drunk->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'drunk' => WerewolfGame::$leftAnonymRole]))
            ->assertOk();

        // insomniac should just see themselves

        // check start distribution
        $this->assertSame(WerewolfGame::WEREWOLF, $game->playerRoles->get($werewolf->id));
        $this->assertSame(WerewolfGame::SEER, $game->playerRoles->get($seer->id));
        $this->assertSame(WerewolfGame::ROBBER, $game->playerRoles->get($robber->id));
        $this->assertSame(WerewolfGame::TROUBLEMAKER, $game->playerRoles->get($troublemaker->id));
        $this->assertSame(WerewolfGame::INSOMNIAC, $game->playerRoles->get($insomniac->id));
        $this->assertSame(WerewolfGame::DRUNK, $game->playerRoles->get($drunk->id));

        // make sunrise
        $this->actingAs($game->hostUser)
            ->post(route('werewolf.sunrise', ['game' => $game->uuid]))
            ->assertOk();

        $game->refresh();

        // werewolf sees left card
        $this->assertSame(WerewolfGame::VILLAGER, $werewolf->moves()->latest()->first()->getPayloadWithKey('sawRole'));

        // seer sees werewolf
        $this->assertSame(WerewolfGame::WEREWOLF, $seer->moves()->latest()->first()->getPayloadWithKey('sawRole'));

        // robber robs werewolf
        $this->assertSame(WerewolfGame::WEREWOLF, $robber->moves()->latest()->first()->getPayloadWithKey('becameRole'));

        // troublemaker switches robber and insomniac
        $this->assertSame(WerewolfGame::WEREWOLF, $troublemaker->moves()->latest()->first()->getPayloadWithKey('switched1Role'));
        $this->assertSame(WerewolfGame::INSOMNIAC, $troublemaker->moves()->latest()->first()->getPayloadWithKey('switched2Role'));

        // drunk takes left card
        $this->assertSame(__('werewolf.player.anonymous_drunk_role'), $drunk->moves()->latest()->first()->getPayloadWithKey('becameRole'));

        // insomniac see themselves
        $this->assertSame(WerewolfGame::WEREWOLF, $insomniac->moves()->latest()->first()->getPayloadWithKey('sawRole'));

        // check correct distribution
        $this->assertSame(WerewolfGame::ROBBER, $game->newPlayerRoles->get($werewolf->id));
        $this->assertSame(WerewolfGame::SEER, $game->newPlayerRoles->get($seer->id));
        $this->assertSame(WerewolfGame::INSOMNIAC, $game->newPlayerRoles->get($robber->id));
        $this->assertSame(WerewolfGame::TROUBLEMAKER, $game->newPlayerRoles->get($troublemaker->id));
        $this->assertSame(WerewolfGame::WEREWOLF, $game->newPlayerRoles->get($insomniac->id));
        $this->assertSame(WerewolfGame::VILLAGER, $game->newPlayerRoles->get($drunk->id));
    }

    /** @test */
    public function vote_for_werewolf()
    {
        /** @var \App\Models\WerewolfGame $game */
        $game = WerewolfGame::factory()->dayWithAllRoles()->create();
        /** @var Player $werewolf */
        $werewolf = $game->playerWithRole(WerewolfGame::WEREWOLF)->first();

        $game->players->map(fn (Player $player) => $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'vote' => $werewolf->id]))
            ->assertOk());

        // make sunrise
        $this->actingAs($game->hostUser)
            ->post(route('werewolf.vote', ['game' => $game->uuid]))
            ->assertOk();

        $game->refresh();

        $this->assertSame('end', $game->currentPayloadAttribute('state'));
        $this->assertSame(WerewolfGame::VILLAGER, $game->currentPayloadAttribute('win'));
        $this->assertSame($werewolf->id, $game->currentPayloadAttribute('killedPlayerId'));
        $this->assertSame(WerewolfGame::WEREWOLF, $game->currentPayloadAttribute('killedRole'));
        $this->assertCount($game->players()->count(), $game->currentRound->moves()->get());
        $this->assertSame(0, $game->currentMoveFromPlayer($werewolf)->score);
    }

    /** @test */
    public function vote_for_villager()
    {
        /** @var \App\Models\WerewolfGame $game */
        $game = WerewolfGame::factory()->dayWithAllRoles()->create();
        /** @var Player $villager */
        $villager = $game->playerWithRole(WerewolfGame::SEER)->first();

        $game->players->map(fn (Player $player) => $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'vote' => $villager->id]))
            ->assertOk());

        // make sunrise
        $this->actingAs($game->hostUser)
            ->post(route('werewolf.vote', ['game' => $game->uuid]))
            ->assertOk();

        $game->refresh();

        $this->assertSame('end', $game->currentPayloadAttribute('state'));
        $this->assertSame(WerewolfGame::WEREWOLF, $game->currentPayloadAttribute('win'));
        $this->assertSame($villager->id, $game->currentPayloadAttribute('killedPlayerId'));
        $this->assertSame(WerewolfGame::SEER, $game->currentPayloadAttribute('killedRole'));
        $this->assertCount($game->players()->count(), $game->currentRound->moves()->get());
        $this->assertSame(0, $game->currentMoveFromPlayer($villager)->score);
    }

    /** @test */
    public function vote_for_minion()
    {
        /** @var \App\Models\WerewolfGame $game */
        $game = WerewolfGame::factory()->dayWithAllRoles()->create();
        /** @var Player $minion */
        $minion = $game->playerWithRole(WerewolfGame::MINION)->first();

        $game->players->map(fn (Player $player) => $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'vote' => $minion->id]))
            ->assertOk());

        // make sunrise
        $this->actingAs($game->hostUser)
            ->post(route('werewolf.vote', ['game' => $game->uuid]))
            ->assertOk();

        $game->refresh();

        $this->assertSame('end', $game->currentPayloadAttribute('state'));
        $this->assertSame(WerewolfGame::WEREWOLF, $game->currentPayloadAttribute('win'));
        $this->assertSame($minion->id, $game->currentPayloadAttribute('killedPlayerId'));
        $this->assertSame(WerewolfGame::MINION, $game->currentPayloadAttribute('killedRole'));
        $this->assertCount($game->players()->count(), $game->currentRound->moves()->get());
        $this->assertSame(1, $game->currentMoveFromPlayer($minion)->score);
    }

    /** @test */
    public function vote_for_tanner()
    {
        /** @var \App\Models\WerewolfGame $game */
        $game = WerewolfGame::factory()->dayWithAllRoles()->create();
        /** @var Player $tanner */
        $tanner = $game->playerWithRole(WerewolfGame::TANNER)->first();

        $game->players->map(fn (Player $player) => $this->actingAs($player->user)
            ->post(route('werewolf.move', ['game' => $game->uuid, 'vote' => $tanner->id]))
            ->assertOk());

        // make sunrise
        $this->actingAs($game->hostUser)
            ->post(route('werewolf.vote', ['game' => $game->uuid]))
            ->assertOk();

        $game->refresh();

        $this->assertSame('end', $game->currentPayloadAttribute('state'));
        $this->assertSame(WerewolfGame::TANNER, $game->currentPayloadAttribute('win'));
        $this->assertSame($tanner->id, $game->currentPayloadAttribute('killedPlayerId'));
        $this->assertSame(WerewolfGame::TANNER, $game->currentPayloadAttribute('killedRole'));
        $this->assertCount($game->players()->count(), $game->currentRound->moves()->get());
        $this->assertSame(1, $game->currentMoveFromPlayer($tanner)->score);
    }

}
