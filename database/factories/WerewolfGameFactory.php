<?php

namespace Database\Factories;

use App\Models\Round;
use App\Models\Player;
use App\Models\Experience;
use Illuminate\Support\Str;
use App\Models\UserActivity;
use App\Models\WerewolfGame;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'uuid'             => Str::uuid(),
            'token'            => Str::upper(Str::random(5)),
            'logic_identifier' => WerewolfGame::$logic_identifier,
            'host_user_id'     => null,
            'started_at'       => null,
            'ended_at'         => null,
        ];
    }

    public function withHostUser()
    {
        return $this->afterCreating(function (WerewolfGame $game) {
            $game->update(['host_user_id' => $game->players->first()->user_id]);
        });
    }

    public function startedWithAllRoles()
    {
        return $this
            ->state(['started_at' => now()])
            ->afterCreating(function (WerewolfGame $game) {
                $players = Player::factory()->state(['game_id' => $game->id])->count(10)->create();
                $game->update(['host_user_id' => $players->first()->user_id]);

                // assign roles
                $roles       = [
                    WerewolfGame::WEREWOLF,
                    WerewolfGame::MASON,
                    WerewolfGame::MINION,
                    WerewolfGame::SEER,
                    WerewolfGame::ROBBER,
                    WerewolfGame::TROUBLEMAKER,
                    WerewolfGame::VILLAGER,
                    WerewolfGame::DRUNK,
                    WerewolfGame::TANNER,
                    WerewolfGame::INSOMNIAC,
                    WerewolfGame::WATCHER,
                ];
                $playerRoles = $game->players->pluck('id')->mapWithKeys(fn ($id, $index) => [$id => $roles[$index]]);

                Round::create([
                    'game_id' => $game->id,
                    'payload' => [
                        'state'       => 'night',
                        'playerRoles' => $playerRoles,
                        'extraRoles'  => [
                            WerewolfGame::$leftAnonymRole   => WerewolfGame::VILLAGER,
                            WerewolfGame::$centerAnonymRole => WerewolfGame::MINION,
                            WerewolfGame::$rightAnonymRole  => WerewolfGame::WEREWOLF,
                        ],
                    ],
                ]);
            });
    }

    public function dayWithAllRoles()
    {
        return $this
            ->state(['started_at' => now()])
            ->afterCreating(function (WerewolfGame $game) {
                $players = Player::factory()->state(['game_id' => $game->id])->count(10)->create();
                $game->update(['host_user_id' => $players->first()->user_id]);

                // assign roles
                $roles       = [
                    WerewolfGame::WEREWOLF,
                    WerewolfGame::MASON,
                    WerewolfGame::MINION,
                    WerewolfGame::SEER,
                    WerewolfGame::ROBBER,
                    WerewolfGame::TROUBLEMAKER,
                    WerewolfGame::VILLAGER,
                    WerewolfGame::DRUNK,
                    WerewolfGame::TANNER,
                    WerewolfGame::INSOMNIAC,
                    WerewolfGame::WATCHER,
                ];
                $playerRoles = $game->players->pluck('id')->mapWithKeys(fn ($id, $index) => [$id => $roles[$index]]);

                Round::create([
                    'game_id' => $game->id,
                    'payload' => [
                        'state'       => 'day',
                        'playerRoles' => $playerRoles,
                        'extraRoles'  => [
                            WerewolfGame::$leftAnonymRole   => WerewolfGame::VILLAGER,
                            WerewolfGame::$centerAnonymRole => WerewolfGame::MINION,
                            WerewolfGame::$rightAnonymRole  => WerewolfGame::WEREWOLF,
                        ],
                    ],
                ]);
            });
    }
}
