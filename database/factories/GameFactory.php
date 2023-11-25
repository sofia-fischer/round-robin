<?php

namespace Database\Factories;

use App\Models\Game;
use App\Models\Player;
use App\Models\Round;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\JustOneGame;
use App\Models\WaveLengthGame;
use Illuminate\Database\Eloquent\Factories\Factory;

class GameFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Game::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'token'            => Str::upper(Str::random(5)),
            'logic_identifier' => WaveLengthGame::$logic_identifier,
            'host_user_id'     => User::factory(),
            'started_at'       => null,
            'ended_at'         => null,
        ];
    }

    /**
     * @return self
     */
    public function withHostUser(): self
    {
        return $this->afterCreating(function (Game $game) {
            Player::create([
                'game_id' => $game->id,
                'user_id' => $game->host_user_id,
            ]);
        });
    }

    public function wavelength(): self
    {
        return $this->state([
            'logic_identifier' => WaveLengthGame::$logic_identifier,
        ]);
    }

    public function started(): self
    {
        return $this->state([
            'started_at' => now(),
        ]);
    }
}
