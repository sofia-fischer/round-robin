<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Player;
use App\Models\User;
use App\Models\WerewolfGame;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WerewolfGameFactory extends GameFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = WerewolfGame::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'token' => Str::upper(Str::random(5)),
            'logic_identifier' => WerewolfGame::$logic_identifier,
            'host_user_id' => User::factory(),
            'started_at' => null,
            'ended_at' => null,
        ];
    }

    public function create($attributes = [], Model|null $parent = null): WerewolfGame
    {
        return parent::create($attributes, $parent);
    }
}
