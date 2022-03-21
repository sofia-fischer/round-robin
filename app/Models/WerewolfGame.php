<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Queue\Events\GameEnded;
use Illuminate\Support\Collection;
use App\Jobs\OneNightWerewolfDayJob;
use App\Queue\Events\GameRoundAction;
use App\Jobs\OneNightWerewolfNightJob;
use App\Support\Enums\WerewolfRoleEnum;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Game
 *
 * @property-read bool $isDay
 * @see \App\Models\WerewolfGame::getIsDayAttribute()
 * @property-read bool $isNight
 * @see \App\Models\WerewolfGame::getIsNightAttribute()
 * @property-read bool $isStart
 * @see \App\Models\WerewolfGame::getIsStartAttribute()
 * @property-read bool $isEnd
 * @see \App\Models\WerewolfGame::getIsEndAttribute()
 * @property-read \Illuminate\Support\Collection $playerRoles
 * @see \App\Models\WerewolfGame::getPlayerRolesAttribute()
 * @property-read \Illuminate\Support\Collection $extraRoles
 * @see \App\Models\WerewolfGame::getExtraRolesAttribute()
 * @property-read \Illuminate\Support\Collection $newPlayerRoles
 * @see \App\Models\WerewolfGame::getNewPlayerRolesAttribute()
 * @property-read \Illuminate\Support\Collection $newExtraRoles
 * @see \App\Models\WerewolfGame::getNewExtraRolesAttribute()
 * @property-read string $authenticatedRole
 * @see \App\Models\WerewolfGame::getAuthenticatedRoleAttribute()
 * @property-read \App\Models\Player|null $authenticatedPlayerVote
 * @see \App\Models\WerewolfGame::getAuthenticatedPlayerVoteAttribute()
 *
 * @package App\Models
 */
class WerewolfGame extends Game
{
    protected $table = 'games';
    static $logic_identifier = 'werewolf';

    static $title = 'One Night Werewolf';

    static $description = 'Each player takes on the role of a Villager, a Werewolf, or a special character.
          It is your job to figure out who the Werewolves are and to kill at least one of them in order to win
          ....unless you have become a Werewolf yourself.
          You wiII need to figure out what team you are on
          (because your role card might have been switched with another role card),
          and then figure out what teams the other players are on.
          At the end of each game you will vote for a player who is not on your team;
          the player that receives the most votes is "killed".';

    public static function query(): Builder
    {
        return parent::query()->where('logic_identifier', self::$logic_identifier);
    }

    protected function getIsStartAttribute()
    {
        return ! $this->started_at;
    }

    protected function getIsDayAttribute()
    {
        return $this->currentRound->payloadAttribute('state') === 'day';
    }

    protected function getIsNightAttribute()
    {
        return $this->currentRound->payloadAttribute('state') === 'night';
    }

    protected function getIsEndAttribute()
    {
        return $this->currentRound->payloadAttribute('state') === 'end';
    }

    protected function getExtraRolesAttribute()
    {
        return collect($this->currentPayloadAttribute('extraRoles', []));
    }

    protected function getPlayerRolesAttribute()
    {
        return collect($this->currentPayloadAttribute('playerRoles', []));
    }

    protected function getNewPlayerRolesAttribute()
    {
        return collect($this->currentPayloadAttribute('newPlayerRoles', $this->playerRoles));
    }

    protected function getNewExtraRolesAttribute()
    {
        return collect($this->currentPayloadAttribute('newExtraRoles', $this->extraRoles));
    }

    public function playerWithRole(string $needleRole): Collection
    {
        $playerIds = $this->playerRoles->filter(fn ($role, $playerId) => $role === $needleRole)->keys();

        return $this->players->whereIn('id', $playerIds);
    }

    public function currentMoveFromPlayer(Player $player): ?Move
    {
        return $player->moves->firstWhere('round_id', $this->currentRound->id);
    }

    private function lookAtCurrentMove(Player $player, string|int $key): ?array
    {
        $identifier = $this->currentMoveFromPlayer($player)->payloadAttribute($key);

        if (! $identifier) {
            return null;
        }

        if (! is_numeric($identifier)) {
            return [
                'sawName'  => $identifier,
                'sawColor' => null,
                'sawRole'  => $this->newExtraRoles->get($identifier),
            ];
        }

        /** @var Player|null $player */
        $player = $this->players->firstWhere('id', $identifier);

        if (! $player) {
            return null;
        }

        return [
            'sawName'  => $player->name,
            'sawColor' => $player->activeColor,
            'sawRole'  => $this->newPlayerRoles->get($player->id),
        ];
    }

    public function switchRoles(string|int $target1, string|int $target2): ?array
    {
        /** @var Player|null $targetPlayer1 */
        $targetPlayer1 = is_numeric($target1) ? $this->players->firstWhere('id', $target1) : null;
        /** @var Player|null $targetPlayer2 */
        $targetPlayer2 = is_numeric($target2) ? $this->players->firstWhere('id', $target2) : null;
        $role1         = $targetPlayer1 ? $this->newPlayerRoles->get($target1) : $this->newExtraRoles->get($target1);
        $role2         = $targetPlayer2 ? $this->newPlayerRoles->get($target2) : $this->newExtraRoles->get($target2);

        $result = [
            'switched1Name'  => $targetPlayer1 ? $targetPlayer1->name : $target1,
            'switched1Color' => $targetPlayer1 ? $targetPlayer1->activeColor : null,
            'switched1Role'  => $role1,
            'switched2Name'  => $targetPlayer2 ? $targetPlayer2->name : $target2,
            'switched2Color' => $targetPlayer2 ? $targetPlayer2->activeColor : null,
            'switched2Role'  => $role2,
        ];

        switch (true) {
            case is_numeric($target1) && is_numeric($target2):
                $this->addCurrentPayloadAttribute('newPlayerRoles', $this->newPlayerRoles->merge([
                    $target1 => $role2,
                    $target2 => $role1,
                ]));
                break;
            case is_numeric($target1) && ! is_numeric($target2):
                $this->addCurrentPayloadAttribute('newPlayerRoles', $this->newPlayerRoles->merge([$target1 => $role2]));
                $this->addCurrentPayloadAttribute('newPlayerRoles', $this->newExtraRoles->merge([$target2 => $role1]));
                break;
            case (! is_numeric($target1)) && is_numeric($target2):
                $this->addCurrentPayloadAttribute('newPlayerRoles', $this->newExtraRoles->merge([$target1 => $role2]));
                $this->addCurrentPayloadAttribute('newPlayerRoles', $this->newPlayerRoles->merge([$target2 => $role1]));
                break;
            case (! is_numeric($target1)) && ! is_numeric($target2):
                $this->addCurrentPayloadAttribute('newPlayerRoles', $this->newExtraRoles->merge([
                    $target1 => $role2,
                    $target2 => $role1,
                ]));
                break;
        };

        return $result;
    }

    public function getAuthenticatedRoleAttribute(): string
    {
        return $this->playerRoles->get($this->authenticatedPlayer->id) ?? WerewolfRoleEnum::WATCHER;
    }

    public function getAuthenticatedPlayerVoteAttribute(): ?Player
    {
        return $this->players
            ->firstWhere('id', $this->currentMoveFromPlayer($this->authenticatedPlayer)
                ?->payloadAttribute('vote'));
    }

    private function generateRoles($currentRoles = [])
    {
        $currentRoles = Collection::wrap($currentRoles);

        if ($currentRoles->count() == $this->players->count() + 3) {
            return $currentRoles;
        }

        if (! $currentRoles->contains(WerewolfRoleEnum::WEREWOLF)) {
            $currentRoles->push(WerewolfRoleEnum::WEREWOLF);
            $currentRoles->push(WerewolfRoleEnum::VILLAGER);
            $currentRoles->push(WerewolfRoleEnum::SEER);

            return $this->generateRoles($currentRoles);
        }

        if (($currentRoles->count() + 2 <= $this->players->count() + 3) && (rand(1, 100) <= 20)) {
            $currentRoles->push(WerewolfRoleEnum::MASON);
            $currentRoles->push(WerewolfRoleEnum::MASON);

            return $this->generateRoles($currentRoles);
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

        return $this->generateRoles($currentRoles);
    }

    public function sunset()
    {
        $roles       = $this->generateRoles()->shuffle()->values();
        $playerRoles = $this->players->pluck('id')->mapWithKeys(fn ($id, $index) => [$id => $roles[$index]]);

        Round::create([
            'uuid'    => Str::uuid(),
            'game_id' => $this->id,
            'payload' => [
                'state'       => 'night',
                'playerRoles' => $playerRoles,
                'extraRoles'  => $roles->slice(-3, 3)->values(),
            ],
        ]);

        event(new GameRoundAction($this));
        OneNightWerewolfNightJob::dispatch($this->id)->onConnection('redis')->delay(now()->addSeconds(WerewolfRoleEnum::NIGHT_DURATION));
    }

    public function sunrise()
    {
        //    Werewolves
        $this->playerWithRole(WerewolfRoleEnum::WEREWOLF)
            ->filter(fn (Player $player) => $this->currentMoveFromPlayer($player))
            ->each(fn (Player $player) => $this->currentMoveFromPlayer($player)
                ->mergePayloadAttribute($this->lookAtCurrentMove($player, 'see')));

        //    Minion
        //    Masons
        //    Seer
        $this->playerWithRole(WerewolfRoleEnum::SEER)
            ->filter(fn (Player $player) => $this->currentMoveFromPlayer($player))
            ->each(fn (Player $player) => $this->currentMoveFromPlayer($player)
                ->mergePayloadAttribute($this->lookAtCurrentMove($player, 'see')));

        //    Robber
        $this->playerWithRole(WerewolfRoleEnum::ROBBER)
            ->filter(fn (Player $player) => $this->currentMoveFromPlayer($player))
            ->each(function (Player $player) {
                $result = $this->switchRoles($player->id, $this->currentMoveFromPlayer($player)->payloadAttribute('steal'));
                $this->currentMoveFromPlayer($player)->mergePayloadAttribute([
                    'becameName'  => $result['switched2Name'],
                    'becameColor' => $result['switched2Color'],
                    'becameRole'  => $result['switched2Color'],
                ]);
            });

        //    Troublemaker
        $this->playerWithRole(WerewolfRoleEnum::TROUBLEMAKER)
            ->filter(fn (Player $player) => $this->currentMoveFromPlayer($player))
            ->map(fn (Player $player) => $this->currentMoveFromPlayer($player)
                ->mergePayloadAttribute($this->switchRoles(
                    $this->currentMoveFromPlayer($player)->payloadAttribute('switch1') ?? $player->id,
                    $this->currentMoveFromPlayer($player)->payloadAttribute('switch2') ?? $player->id
                )));

        //    Drunk
        $this->playerWithRole(WerewolfRoleEnum::DRUNK)
            ->filter(fn (Player $player) => $this->currentMoveFromPlayer($player))
            ->each(function (Player $player) {
                $result = $this->switchRoles($player->id, $this->currentMoveFromPlayer($player)->payloadAttribute('drunk'));
                $this->currentMoveFromPlayer($player)->mergePayloadAttribute([
                    'becameName' => $result['switched2Name'],
                    'becameRole' => 'Something else...',
                ]);
            });

        //    Insomniac
        $this->playerWithRole(WerewolfRoleEnum::INSOMNIAC)
            ->each(function (Player $player) {
                Move::updateOrCreate([
                    'round_id'  => $this->currentRound->id,
                    'player_id' => $player->id,
                    'user_id'   => $player->user_id,
                    'payload'   => ['see' => $player->id]
                ]);

                $this->currentMoveFromPlayer($player)->mergePayloadAttribute($this->lookAtCurrentMove($player, 'see'));
            });

        $this->addCurrentPayloadAttribute('state', 'day');
        event(new GameRoundAction($this));
        OneNightWerewolfDayJob::dispatch($this->id)->onConnection('redis')->delay(now()->addSeconds(WerewolfRoleEnum::DAY_DURATION));
    }


    public function vote()
    {
        $killedPlayerId = $this->players
            ->mapWithKeys(fn (Player $player) => [
                $player->id => $this->currentMoveFromPlayer($player)?->payloadAttribute('vote'),
            ])
            ->countBy()
            ->sortDesc()
            ->keys()
            ->first();

        $killedPlayerRole = $killedPlayerId ? $this->newPlayerRoles->get($killedPlayerId) : null;

        $win = match ($killedPlayerRole) {
            WerewolfRoleEnum::TANNER => WerewolfRoleEnum::TANNER,
            WerewolfRoleEnum::WEREWOLF => WerewolfRoleEnum::VILLAGER,
            default => $this->newPlayerRoles->filter(fn ($role, $playerId) => $role === WerewolfRoleEnum::WEREWOLF)->count()
                ? WerewolfRoleEnum::WEREWOLF : WerewolfRoleEnum::VILLAGER,
        };

        $this->players->each(fn (Player $player) => Move::updateOrCreate([
            'round_id'  => $this->currentRound->id,
            'player_id' => $player->id,
            'user_id'   => $player->user_id,
        ], [
            'score' => match ($this->newPlayerRoles->get($player->id)) {
                WerewolfRoleEnum::WEREWOLF => $win === WerewolfRoleEnum::WEREWOLF,
                WerewolfRoleEnum::MINION => $win === WerewolfRoleEnum::WEREWOLF,
                WerewolfRoleEnum::TANNER => $win === WerewolfRoleEnum::TANNER,
                WerewolfRoleEnum::WATCHER => 0,
                default => $win == WerewolfRoleEnum::VILLAGER
            },
        ]));

        $killledPlayer = $this->players->firstWhere('id', $killedPlayerId);
        $this->mergeCurrentPayloadAttribute([
            'state'             => 'end',
            'win'               => $win,
            'killedPlayerId'    => $killedPlayerId,
            'killedPlayerName'  => $killledPlayer?->name ?? 'Nobody',
            'killedPlayerColor' => $killledPlayer->activeColor ?? 'gray-500',
            'killedRole'        => $killedPlayerRole,
        ]);

        $this->currentRound->completed_at = now();
        $this->currentRound->save();
        event(new GameEnded($this->id));
    }
}
