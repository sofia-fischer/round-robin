<?php

namespace App\Support\GamePolicies;

use App\Jobs\OneNightWerewolfDayJob;
use App\Jobs\OneNightWerewolfNightJob;
use App\Models\Game;
use App\Models\Move;
use App\Models\Player;
use App\Models\Round;
use App\Queue\Events\GameEnded;
use App\Queue\Events\GameRoundAction;
use App\Queue\Events\GameStarted;
use App\Support\Enums\WerewolfRoleEnum;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OneNightWerewolfPolicy extends Policy
{
    public function startGame(Game $game)
    {
        if (!$game->started_at) {
            $game->started_at = now();
            $game->save();
        }

        $roles = collect($this->getRoles($game->players()->count()))->shuffle();
        $playerRoles = $game->players()->pluck('id')->mapWithKeys(function ($id, $index) use ($roles) {
            return [$id => $roles[$index]];
        });

        Round::create([
            'uuid'    => Str::uuid(),
            'game_id' => $game->id,
            'payload' => [
                'state'       => 'night',
                'playerRoles' => $playerRoles,
                'extraRoles'  => $roles->slice(-3, 3)->values(),
            ],
        ]);
        event(new GameStarted($game->id));

        OneNightWerewolfNightJob::dispatch($game->id)->onConnection('redis')->delay(now()->addSeconds(WerewolfRoleEnum::NIGHT_DURATION));
    }

    private function getRoles(int $playerCount, $currentRoles = [])
    {
        $currentRoles = Collection::wrap($currentRoles);

        if ($currentRoles->count() == $playerCount + 3) {
            return $currentRoles;
        }

        if (!$currentRoles->contains(WerewolfRoleEnum::WEREWOLF)) {
            $currentRoles->push(WerewolfRoleEnum::WEREWOLF);
            $currentRoles->push(WerewolfRoleEnum::VILLAGER);
            $currentRoles->push(WerewolfRoleEnum::SEER);

            return $this->getRoles($playerCount, $currentRoles);
        }

        if (($currentRoles->count() + 2 <= $playerCount + 3) && (rand(1, 100) <= 20)) {
            $currentRoles->push(WerewolfRoleEnum::MASON);
            $currentRoles->push(WerewolfRoleEnum::MASON);

            return $this->getRoles($playerCount, $currentRoles);
        }

        $randomRole = collect([
            WerewolfRoleEnum::WEREWOLF,
            WerewolfRoleEnum::SEER,
            WerewolfRoleEnum::ROBBER,
            WerewolfRoleEnum::TROUBLEMAKER,
            WerewolfRoleEnum::TROUBLEMAKER,
            WerewolfRoleEnum::VILLAGER,
            WerewolfRoleEnum::VILLAGER,
            WerewolfRoleEnum::DRUNK,
            WerewolfRoleEnum::TANNER,
            WerewolfRoleEnum::INSOMNIAC,
            WerewolfRoleEnum::INSOMNIAC,
            WerewolfRoleEnum::MINION,
        ])->random();

        $currentRoles->push($randomRole);

        return $this->getRoles($playerCount, $currentRoles);
    }

    public function playerJoined(Player $player, Game $game)
    {
    }

    public function roundAction(Round $round, array $options = [])
    {
        /** @var Move $move */
        $move = Move::updateOrCreate([
            'round_id'  => $round->id,
            'player_id' => $round->game->authenticatedPlayer->id,
        ], [
            'user_id' => Auth::id(),
            'payload' => $options,
        ]);
    }

    static function calculateSunrise(Round $round)
    {
        // insomniac
        $insomniacIds = collect($round->payload['playerRoles'])->filter(function ($role) {
            return $role == WerewolfRoleEnum::INSOMNIAC;
        })->keys();

        $insomniacPlayers = Player::findMany($insomniacIds)->map(function (Player $player) use ($round) {
            $move = Move::updateOrCreate([
                'round_id'  => $round->id,
                'player_id' => $round->game->authenticatedPlayer->id,
                'user_id'   => $player->user_id,
            ], [
                'payload' => [
                    'see' => $player->id,
                ],
            ]);
        });

        $oldPlayerRoles = $round->payload['playerRoles'];
        $newPlayerRoles = $round->payload['playerRoles'];
        $extraRoles = $round->payload['extraRoles'];

        $round->moves()->orderBy('created_at', 'DESC')->get()
            ->map(function (Move $move) use (&$newPlayerRoles, &$extraRoles) {
                if (!$move->payload) {
                    return;
                }

                $movePayload = $move->payload;

                if ($movePayload['anonymous'] ?? false) {
                    $playerRole = $newPlayerRoles[$move->player_id];
                    $anonymousRole = $extraRoles[$movePayload['anonymous'] - 1];
                    $newPlayerRoles[$move->player_id] = $anonymousRole;
                    $extraRoles[$movePayload['anonymous'] - 1] = $playerRole;
                }

                if ($movePayload['see'] ?? false) {
                    $movePayload['saw'] = $newPlayerRoles[$movePayload['see']];
                }

                if ($movePayload['seeAnonymous'] ?? false) {
                    $movePayload['sawAnonymous'] = $extraRoles[$movePayload['seeAnonymous'] - 1];
                }

                if ($movePayload['switch1'] ?? false && $movePayload['switch2'] ?? false) {
                    $roleOne = Str::startsWith($movePayload['switch1'], 'anonymous-')
                        ? $extraRoles[(int) Str::replaceFirst('anonymous-', '', $movePayload['switch1'])]
                        : $newPlayerRoles[$movePayload['switch1']];
                    $roleTwo = Str::startsWith($movePayload['switch2'], 'anonymous-')
                        ? $extraRoles[(int) Str::replaceFirst('anonymous-', '', $movePayload['switch2'])]
                        : $newPlayerRoles[$movePayload['switch2']];
                    $newPlayerRoles[$movePayload['switch1']] = $roleTwo;
                    $newPlayerRoles[$movePayload['switch2']] = $roleOne;
                }

                $move->payload = $movePayload;
                $move->save();
            });

        $round->payload = [
            'state'          => 'day',
            'playerRoles'    => $oldPlayerRoles,
            'newPlayerRoles' => $newPlayerRoles,
            'extraRoles'     => $extraRoles,
        ];
        $round->save();

        event(new GameRoundAction($round->game_id));
        OneNightWerewolfDayJob::dispatch($round->game_id)->onConnection('redis')->delay(now()->addSeconds(WerewolfRoleEnum::DAY_DURATION));
    }

    static function calculateResults(Round $round)
    {
        $players = $round->game->players->map(function (Player $player) use ($round) {
            $player->role = $round->payload['newPlayerRoles'][$player->id];

            /** @var Move $move */
            $move = $round->moves->firstWhere('player_id', $player->id);
            if (!$move) {
                $move = Move::create([
                    'round_id'  => $round->id,
                    'player_id' => $player->id,
                    'user_id'   => $player->user_id,
                    'payload'   => [
                        'vote' => null,
                    ],
                ]);
            }

            if (!($move->payload['vote'] ?? false)) {
                $payload = $move->payload;
                $payload['vote'] = null;
                $move->payload = $payload;
                $move->save();
            }

            $player->vote = $move->payload['vote'] ?? null;

            return $player;
        });


        $votedPlayerId = $players->countBy('vote')->sortDesc()->keys()->first();
        $killedPlayer = $players->find($votedPlayerId);

        $win = null;

        if (!$killedPlayer) {
            $livingWerewolf = $villagersWin = $players->filter(function (Player $player) {
                return $player->role != WerewolfRoleEnum::WEREWOLF;
            })->isNotEmpty();
            $win = $livingWerewolf ? WerewolfRoleEnum::WEREWOLF : WerewolfRoleEnum::VILLAGER;
        } elseif ($killedPlayer->role == WerewolfRoleEnum::TANNER) {
            $win = WerewolfRoleEnum::TANNER;
        } else {
            $win = $killedPlayer->role == WerewolfRoleEnum::WEREWOLF ? WerewolfRoleEnum::VILLAGER : WerewolfRoleEnum::WEREWOLF;
        }

        $round->moves()->get()->map(function (Move $move) use ($players, $win) {
            $role = $players->find($move->player_id)->role ?? null;

            if ($role == $win) {
                $move->score = 1;
                $move->save();

                return;
            }

            if ($win == WerewolfRoleEnum::TANNER) {
                return;
            }

            if ($role == WerewolfRoleEnum::MINION && $win != WerewolfRoleEnum::WEREWOLF) {
                return;
            }

            if ($role == WerewolfRoleEnum::WEREWOLF && $win != WerewolfRoleEnum::WEREWOLF) {
                return;
            }

            $move->score = 1;
            $move->save();
        });

        $payload = $round->payload;
        $payload['win'] = $win;
        $round->payload = $payload;
        $round->completed_at = now();
        $round->save();
        event(new GameEnded($round->game_id));
    }

    public function endRound(Round $round)
    {
        $this->startGame($round->game);
    }
}
