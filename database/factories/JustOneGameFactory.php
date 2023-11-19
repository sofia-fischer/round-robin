<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Player;
use App\Models\Round;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\JustOneGame;
use Illuminate\Database\Eloquent\Factories\Factory;

class JustOneGameFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = JustOneGame::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'token'            => Str::upper(Str::random(5)),
            'logic_identifier' => JustOneGame::$logic_identifier,
            'host_user_id'     => User::factory(),
            'started_at'       => now(),
            'ended_at'         => null,
        ];
    }

    public function withRound(string $word = null): self
    {
        return $this->afterCreating(fn (JustOneGame $game) => Round::factory()
            ->create([
                'game_id'          => $game->id,
                'active_player_id' => $game->hostPlayer->id,
                'payload'          => ['word' => $word ?? collect(__('words'))->random(),],
            ]));
    }

    /**
     * @return self
     */
    public function withHostUser(): self
    {
        return $this->afterCreating(function (JustOneGame $game) {
            Player::create([
                'game_id' => $game->id,
                'user_id' => $game->host_user_id,
            ]);
        });
    }
}
