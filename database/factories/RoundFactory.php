<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Round;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoundFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Round::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'game_id'          => null,
            'active_player_id' => null,
            'completed_at'     => null,
            'payload'          => [],
        ];
    }
}
