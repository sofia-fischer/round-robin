<?php

namespace Database\Factories;

use App\Models\Round;
use App\Models\Experience;
use Illuminate\Support\Str;
use App\Models\JustOneGame;
use App\Models\UserActivity;
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
            'uuid'             => Str::uuid(),
            'token'            => Str::upper(Str::random(5)),
            'logic_identifier' => JustOneGame::$logic_identifier,
            'host_user_id'     => null,
            'started_at'       => now(),
            'ended_at'         => null,
        ];
    }

    public function withRound(string $word = null)
    {
        return $this->afterCreating(fn (JustOneGame $game) => Round::factory()
            ->create([
                'game_id'          => $game->id,
                'active_player_id' => $game->host_user_id,
                'payload'          => ['word' => $word ?? collect(__('words'))->random(),],
            ]));
    }

    public function withHostUser()
    {
        return $this->afterCreating(function (JustOneGame $game) {
            $game->update(['host_user_id' => $game->players->first()->user_id]);
        });
    }
}
