<?php

namespace Tests\Feature;

use App\Models\PlanetXGame;
use App\Models\User;
use App\Queue\Events\PlayerCreated;
use App\ValueObjects\Enums\PlanetXIconEnum;
use App\ValueObjects\PlanetXRules\CountInManySectorsRule;
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
            ->assertRedirect(route('planet_x.show', ['game' => $game->id]));

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
            ->assertRedirect(route('planet_x.show', ['game' => $game->id]));

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
            ->assertRedirect(route('planet_x.show', ['game' => $game->id]));

        $game->refresh();
        $rules = $game->getAuthenticatedPlayerRules();
        $this->assertCount(1, $rules);
        $rule = $rules[0];
        $this->assertInstanceOf(InSectorRule::class, $rule);
        $this->assertEquals(0, $rule->sector);
    }

    public function testSurvey()
    {
        Event::fake();

        /** @var PlanetXGame $game */
        $game = PlanetXGame::factory()
            ->withHostUser()
            ->withRound()
            ->create();

        // outside observable sky
        $this->actingAs($game->hostUser)
            ->post(route('planet_x.survey', [
                'game' => $game->id,
                'icon' => PlanetXIconEnum::PLANET->value,
                'from' => 8,
                'to' => 11,
            ]))
            ->assertSessionHasErrors(['from', 'to']);
        // planet x survey
        $this->actingAs($game->hostUser)
            ->post(route('planet_x.survey', [
                'game' => $game->id,
                'icon' => PlanetXIconEnum::PLANET_X->value,
                'from' => 1,
                'to' => 5,
            ]))
            ->assertSessionHasErrors(['icon']);

        $this->disableExceptionHandling();
        // valid survey
        $this->actingAs($game->hostUser)
            ->post(route('planet_x.survey', [
                'game' => $game->id,
                'icon' => PlanetXIconEnum::PLANET,
                'from' => 1,
                'to' => 5,
            ]))
            ->assertRedirect(route('planet_x.show', ['game' => $game->id]));

        $game->refresh();
        $rules = $game->getAuthenticatedPlayerRules();
        $this->assertCount(1, $rules);
        $rule = $rules[0];
        $this->assertInstanceOf(CountInManySectorsRule::class, $rule);
        $this->assertEquals(1, $rule->from);
        $this->assertEquals(5, $rule->to);
        $this->assertEquals(PlanetXIconEnum::PLANET, $rule->icon);
    }
}
