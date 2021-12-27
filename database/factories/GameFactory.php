<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Game;
use Illuminate\Support\Str;
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
            'uuid'             => Str::uuid(),
            'token'            => Str::upper(Str::random(5)),
            'logic_identifier' => App\Support\GameLogics\WavelengthLogic::class,
            'host_user_id'     => User::factory(),
            'started_at'       => null,
            'ended_at'         => null,
        ];
    }
}
