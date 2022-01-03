<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Game;
use App\Models\Player;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlayerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Player::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'uuid'    => Str::uuid(),
            'game_id' => Game::factory(),
            'user_id' => User::factory(),
        ];
    }
}
