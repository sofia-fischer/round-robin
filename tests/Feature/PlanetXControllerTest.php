<?php

namespace Tests\Feature;

use App\Models\PlanetXGame;
use App\Models\User;
use App\Queue\Events\PlayerCreated;
use App\ValueObjects\PlanetXRules\InSectorRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PlanetXController
 */
class PlanetXControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testShow()
    {
        $this->disableExceptionHandling();
        Event::fake();

        /** @var User $user */
        $user = User::factory()->create();
        $game = PlanetXGame::factory()->create();

        $this->actingAs($user)
            ->get(route('planet_x.show', ['game' => $game->id]))
            ->assertOk()
            ->assertViewIs('GamePage');

        $this->assertDatabaseHas('players', [
            'game_id' => $game->id,
            'user_id' => $user->id,
        ]);

        Event::assertDispatched(PlayerCreated::class);

        $this->assertDatabaseHas('rounds', [
            'game_id' => $game->id,
        ]);

        $rules = $game->getAuthenticatedPlayerRules();
        $this->assertCount(6, $rules);
    }

    public function testConference()
    {
        $this->disableExceptionHandling();
        Event::fake();

        /** @var PlanetXGame $game */
        $game = PlanetXGame::factory()
            ->withHostUser()
            ->withRound()
            ->create();

        $this->actingAs($game->hostUser)
            ->post(route('planet_x.conference', ['game' => $game->id, 'conference' => 'A']))
            ->assertOk()
            ->assertViewIs('GamePage');

        $game->refresh();
        $conference = $game->getAuthenticatedPlayerConference();
        $this->assertNotNull($conference->alpha);
    }

    public function testTarget()
    {
        $this->disableExceptionHandling();
        Event::fake();

        /** @var PlanetXGame $game */
        $game = PlanetXGame::factory()
            ->withHostUser()
            ->withRound()
            ->create();

        $this->actingAs($game->hostUser)
            ->post(route('planet_x.target', ['game' => $game->id, 'target' => 0]))
            ->assertOk()
            ->assertViewIs('GamePage');

        $game->refresh();
        $rules = $game->getAuthenticatedPlayerRules();
        $this->assertCount(1, $rules);
        $rule = $rules[0];
        $this->assertInstanceOf(InSectorRule::class, $rule);
        $this->assertEquals(0, $rule->sector);
    }
}
