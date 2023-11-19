<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Player;
use App\Models\Round;
use App\Models\User;
use App\Models\WerewolfGame;
use App\ValueObjects\Enums\WerewolfRoleEnum;
use App\ValueObjects\WerewolfBoard;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WerewolfGameFactory extends Factory
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

    /**
     * @return self
     */
    public function withHostUser(): self
    {
        return $this->afterCreating(function (WerewolfGame $game) {
            Player::create([
                'game_id' => $game->id,
                'user_id' => $game->host_user_id,
            ]);
        });
    }

    public function startedWithAllRoles(): self
    {
        return $this
            ->state(['started_at' => now()])
            ->afterCreating(function (WerewolfGame $game) {
                $players = Player::factory()->state(['game_id' => $game->id])->count(10)->create();
                $game->update(['host_user_id' => $players->first()->user_id]);

                $round = WerewolfBoard::newRoundForPlayers($players->pluck('id')->toArray());
                Round::create(['game_id' => $game->id, 'payload' => $round->toArray()]);
            });
    }

    public function dayWithAllRoles(): self
    {
        return $this
            ->state(['started_at' => now()])
            ->afterCreating(function (WerewolfGame $game) {
                $players = Player::factory()->state(['game_id' => $game->id])->count(10)->create();
                $game->update(['host_user_id' => $players->first()->user_id]);

                // assign roles
                $roles = WerewolfRoleEnum::cases();
                $playerRoles = $game->players->pluck('id')->mapWithKeys(fn ($id, $index) => [$id => $roles[$index]]);

                Round::create([
                    'game_id' => $game->id,
                    'payload' => [
                        'state' => 'day',
                        'playerRoles' => $playerRoles,
                        'extraRoles' => [
                            WerewolfGame::$leftAnonymRole => WerewolfRoleEnum::VILLAGER,
                            WerewolfGame::$centerAnonymRole => WerewolfRoleEnum::MINION,
                            WerewolfGame::$rightAnonymRole => WerewolfRoleEnum::WEREWOLF,
                        ],
                    ],
                ]);
            });
    }
}
