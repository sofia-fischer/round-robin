<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Round;
use App\Models\User;
use App\Models\WaveLengthGame;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WavelengthGameFactory extends GameFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = WaveLengthGame::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'token' => Str::upper(Str::random(5)),
            'logic_identifier' => WaveLengthGame::$logic_identifier,
            'host_user_id' => User::factory(),
            'started_at' => now(),
            'ended_at' => null,
        ];
    }

    public function withRound(): self
    {
        return $this->afterCreating(fn (WaveLengthGame $game) => Round::factory()
            ->create([
                'game_id' => $game->id,
                'active_player_id' => $game->hostPlayer->id,
                'payload' => [
                    'waveLength' => random_int(0, 100),
                    'antonym1' => 'antonym1',
                    'antonym2' => 'antonym2',
                ],
            ]));
    }

    public function create($attributes = [], Model|null $parent = null): WaveLengthGame
    {
        return parent::create($attributes, $parent);
    }
}
