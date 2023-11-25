<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\PlanetXGame;
use App\Models\Round;
use App\Models\User;
use App\Services\PlanetXBoardGenerationService;
use App\Services\PlanetXConferenceGenerationService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PlanetXGameFactory extends GameFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PlanetXGame::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'token' => Str::upper(Str::random(5)),
            'logic_identifier' => PlanetXGame::$logic_identifier,
            'host_user_id' => User::factory(),
            'started_at' => null,
            'ended_at' => null,
        ];
    }

    public function withRound(): self
    {
        return $this->afterCreating(function (PlanetXGame $game) {
            $boardGenerationService = app(PlanetXBoardGenerationService::class);
            $conferenceGenerationService = app(PlanetXConferenceGenerationService::class);
            $board = $boardGenerationService->generateBoard();
            $conference = $conferenceGenerationService->generateRulesForConferences($board);

            Round::create([
                'game_id' => $game->id,
                'payload' => [
                    'board' => $board->toArray(),
                    'conference' => $conference->toArray(),
                ],
            ]);
        });
    }

    public function create($attributes = [], Model|null $parent = null): PlanetXGame
    {
        return parent::create($attributes, $parent);
    }
}
