<?php

namespace Database\Factories;

use App\Models\Move;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class MoveFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Move::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'uuid'      => Str::uuid(),
            'round_id'  => null,
            'player_id' => null,
            'user_id'   => null,
            'score'     => null,
            'payload'   => null,
        ];
    }
}
