<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\JustOneGame;
use App\Models\Round;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class JustOneGameFactory extends GameFactory
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
            'token' => Str::upper(Str::random(5)),
            'logic_identifier' => JustOneGame::$logic_identifier,
            'host_user_id' => User::factory(),
            'started_at' => now(),
            'ended_at' => null,
        ];
    }

    public function withRound(string $word = null): self
    {
        return $this->afterCreating(fn (JustOneGame $game) => Round::factory()
            ->create([
                'game_id' => $game->id,
                'active_player_id' => $game->hostPlayer->id,
                'payload' => ['word' => $word ?? collect(__('words'))->random(),],
            ]));
    }

    public function create($attributes = [], Model|null $parent = null): JustOneGame
    {
        return parent::create($attributes, $parent);
    }
}
