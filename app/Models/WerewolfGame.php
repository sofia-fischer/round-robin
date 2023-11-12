<?php

namespace App\Models;

use App\Jobs\OneNightWerewolfDayJob;
use App\Jobs\OneNightWerewolfNightJob;
use App\Queue\Events\GameEnded;
use App\Queue\Events\GameRoundAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

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

    static $leftAnonymRole = 'left';
    static $centerAnonymRole = 'center';
    static $rightAnonymRole = 'right';

    const  WEREWOLF = 'werewolf';
    const  MASON = 'mason';
    const  MINION = 'minion';
    const  SEER = 'seer';
    const  ROBBER = 'robber';
    const  TROUBLEMAKER = 'troublemaker';
    const  VILLAGER = 'villager';
    const  DRUNK = 'drunk';
    const  TANNER = 'tanner';
    const  INSOMNIAC = 'insomniac';
    const  WATCHER = 'watcher';

    public const NIGHT_DURATION = 100;
    public const DAY_DURATION = 200;

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
        return $this->currentRound?->payloadAttribute('state') === 'day';
    }

    protected function getIsNightAttribute()
    {
        return $this->currentRound?->payloadAttribute('state') === 'night';
    }

    protected function getIsEndAttribute()
    {
        return $this->currentRound?->payloadAttribute('state') === 'end';
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
        $identifier = $this->currentMoveFromPlayer($player)->getPayloadWithKey($key);

        if (! $identifier) {
            return null;
        }

        if (! is_numeric($identifier)) {
            return [
                'sawName' => $identifier,
                'sawColor' => null,
                'sawRole' => $this->newExtraRoles->get($identifier),
            ];
        }

        /** @var Player|null $player */
        $player = $this->players->firstWhere('id', $identifier);

        if (! $player) {
            return null;
        }

        return [
            'sawName' => $player->name,
            'sawColor' => $player->activeColor,
            'sawRole' => $this->newPlayerRoles->get($player->id),
        ];
    }

    public function switchRoles(string|int $target1, string|int $target2): ?array
    {
        /** @var Player|null $targetPlayer1 */
        $targetPlayer1 = is_numeric($target1) ? $this->players->firstWhere('id', $target1) : null;
        /** @var Player|null $targetPlayer2 */
        $targetPlayer2 = is_numeric($target2) ? $this->players->firstWhere('id', $target2) : null;
        $role1 = $targetPlayer1 ? $this->newPlayerRoles->get($target1) : $this->newExtraRoles->get($target1);
        $role2 = $targetPlayer2 ? $this->newPlayerRoles->get($target2) : $this->newExtraRoles->get($target2);

        $result = [
            'switched1Name' => $targetPlayer1 ? $targetPlayer1->name : $target1,
            'switched1Color' => $targetPlayer1 ? $targetPlayer1->activeColor : null,
            'switched1Role' => $role1,
            'switched2Name' => $targetPlayer2 ? $targetPlayer2->name : $target2,
            'switched2Color' => $targetPlayer2 ? $targetPlayer2->activeColor : null,
            'switched2Role' => $role2,
        ];

        switch (true) {
            case is_numeric($target1) && is_numeric($target2):
                $this->addCurrentPayloadAttribute('newPlayerRoles', collect([$target1 => $role2, $target2 => $role1])
                    ->union($this->newPlayerRoles));
                break;
            case is_numeric($target1) && ! is_numeric($target2):
                $this->addCurrentPayloadAttribute('newPlayerRoles', collect([$target1 => $role2])->union($this->newPlayerRoles));
                $this->addCurrentPayloadAttribute('newExtraRoles', collect([$target2 => $role1])->union($this->newPlayerRoles));
                break;
            case (! is_numeric($target1)) && is_numeric($target2):
                $this->addCurrentPayloadAttribute('newPlayerRoles', collect([$target2 => $role1])->union($this->newPlayerRoles));
                $this->addCurrentPayloadAttribute('newExtraRoles', collect([$target1 => $role2])->union($this->newExtraRoles));
                break;
            case (! is_numeric($target1)) && ! is_numeric($target2):
                $this->addCurrentPayloadAttribute('newExtraRoles', collect([$target1 => $role2, $target2 => $role1])
                    ->union($this->newExtraRoles));
                break;
        }

        return $result;
    }

    public function getAuthenticatedRoleAttribute(): string
    {
        return $this->playerRoles->get($this->authenticatedPlayer->id) ?? WerewolfGame::WATCHER;
    }

    public function getAuthenticatedPlayerVoteAttribute(): ?Player
    {
        return $this->players
            ->firstWhere('id', $this->currentMoveFromPlayer($this->authenticatedPlayer)
                ?->getPayloadWithKey('vote'));
    }

    public function startRound()
    {
        // generating roles
        $evilRoleCount = floor($this->players->count() / 3) ?? 1;
        $roles = collect([]);

        $werewolfCount = max($evilRoleCount - floor($evilRoleCount / 4), 1);
        Collection::times($werewolfCount)->each(fn ($item) => $roles->push(WerewolfGame::WEREWOLF));
        Collection::times($evilRoleCount - $werewolfCount)->each(fn ($item) => $roles->push(WerewolfGame::MINION));

        Collection::times($this->players->count() + 3 - $evilRoleCount)
            ->each(fn ($item) => $roles->push(collect([
                WerewolfGame::SEER,
                WerewolfGame::ROBBER,
                WerewolfGame::TROUBLEMAKER,
                WerewolfGame::VILLAGER,
                WerewolfGame::VILLAGER,
                WerewolfGame::DRUNK,
                WerewolfGame::TANNER,
                WerewolfGame::INSOMNIAC,
                WerewolfGame::INSOMNIAC,
            ])->random()));

        // assign roles
        $roles = $roles->shuffle()->values();
        $playerRoles = $this->players->pluck('id')->mapWithKeys(fn ($id, $index) => [$id => $roles[$index]])->toArray();
        $extraRoles = $roles->slice(-3, 3)->values();

        Round::create([
            'game_id' => $this->id,
            'payload' => [
                'state' => 'night',
                'playerRoles' => $playerRoles,
                'extraRoles' => [
                    WerewolfGame::$leftAnonymRole => $extraRoles[0],
                    WerewolfGame::$centerAnonymRole => $extraRoles[1],
                    WerewolfGame::$rightAnonymRole => $extraRoles[2],
                ],
            ],
        ]);

        event(new GameRoundAction($this));
        OneNightWerewolfNightJob::dispatch($this->id)->onConnection('redis')->delay(now()->addSeconds(WerewolfGame::NIGHT_DURATION));
    }

    public function sunrise()
    {
        //    Werewolves
        $this->playerWithRole(WerewolfGame::WEREWOLF)
            ->filter(fn (Player $player) => $this->currentMoveFromPlayer($player))
            ->each(fn (Player $player) => $this->currentMoveFromPlayer($player)
                ->mergePayload($this->lookAtCurrentMove($player, 'see')));

        //    Minion
        //    Masons
        //    Seer
        $this->playerWithRole(WerewolfGame::SEER)
            ->filter(fn (Player $player) => $this->currentMoveFromPlayer($player))
            ->each(fn (Player $player) => $this->currentMoveFromPlayer($player)
                ->mergePayload($this->lookAtCurrentMove($player, 'see')));

        //    Robber
        $this->playerWithRole(WerewolfGame::ROBBER)
            ->filter(fn (Player $player) => $this->currentMoveFromPlayer($player))
            ->each(function (Player $player) {
                $result = $this->switchRoles($player->id, $this->currentMoveFromPlayer($player)->getPayloadWithKey('steal'));
                $this->currentMoveFromPlayer($player)->mergePayload([
                    'becameName' => $result['switched2Name'],
                    'becameColor' => $result['switched2Color'],
                    'becameRole' => $result['switched2Role'],
                ]);
            });

        //    Troublemaker
        $this->playerWithRole(WerewolfGame::TROUBLEMAKER)
            ->filter(fn (Player $player) => $this->currentMoveFromPlayer($player))
            ->map(fn (Player $player) => $this->currentMoveFromPlayer($player)
                ->mergePayload($this->switchRoles(
                    $this->currentMoveFromPlayer($player)->getPayloadWithKey('switch1') ?? $player->id,
                    $this->currentMoveFromPlayer($player)->getPayloadWithKey('switch2') ?? $player->id
                )));

        //    Drunk
        $this->playerWithRole(WerewolfGame::DRUNK)
            ->filter(fn (Player $player) => $this->currentMoveFromPlayer($player))
            ->each(function (Player $player) {
                $result = $this->switchRoles($player->id, $this->currentMoveFromPlayer($player)->getPayloadWithKey('drunk'));
                $this->currentMoveFromPlayer($player)->mergePayload([
                    'becameName' => $result['switched2Name'],
                    'becameRole' => __('werewolf.player.anonymous_drunk_role'),
                ]);
            });

        //    Insomniac
        $this->playerWithRole(WerewolfGame::INSOMNIAC)
            ->each(function (Player $player) {
                Move::query()->create([
                    'payload' => ['see' => $player->id],
                    'round_id' => $this->currentRound->id,
                    'user_id' => $player->user_id,
                    'player_id' => $player->id,
                ]);

                $this->currentMoveFromPlayer($player)->mergePayload($this->lookAtCurrentMove($player, 'see'));
            });

        $this->addCurrentPayloadAttribute('state', 'day');
        event(new GameRoundAction($this));
        OneNightWerewolfDayJob::dispatch($this->id)->onConnection('redis')->delay(now()->addSeconds(WerewolfGame::DAY_DURATION));
    }


    public function vote()
    {
        $killedPlayerId = $this->players
            ->mapWithKeys(fn (Player $player) => [
                $player->id => $this->currentMoveFromPlayer($player)?->getPayloadWithKey('vote'),
            ])
            ->countBy()
            ->sortDesc()
            ->keys()
            ->first();

        $killedPlayerRole = $this->newPlayerRoles->get($killedPlayerId);

        $win = match ($killedPlayerRole) {
            WerewolfGame::TANNER => WerewolfGame::TANNER,
            WerewolfGame::WEREWOLF => WerewolfGame::VILLAGER,
            default => $this->newPlayerRoles->filter(fn ($role, $playerId) => $role === WerewolfGame::WEREWOLF)->count()
                ? WerewolfGame::WEREWOLF : WerewolfGame::VILLAGER,
        };

        $this->players->each(fn (Player $player) => Move::updateOrCreate([
            'round_id' => $this->currentRound->id,
            'player_id' => $player->id,
            'user_id' => $player->user_id,
        ], [
            'score' => match ($this->newPlayerRoles->get($player->id)) {
                WerewolfGame::WEREWOLF => $win === WerewolfGame::WEREWOLF ? 1 : 0,
                WerewolfGame::MINION => $win === WerewolfGame::WEREWOLF ? 1 : 0,
                WerewolfGame::TANNER => $win === WerewolfGame::TANNER ? 1 : 0,
                WerewolfGame::WATCHER => 0,
                default => $win === WerewolfGame::VILLAGER ? 1 : 0
            },
        ]));

        $killledPlayer = $this->players->firstWhere('id', $killedPlayerId);
        $this->mergeCurrentPayloadAttribute([
            'state' => 'end',
            'win' => $win,
            'killedPlayerId' => $killedPlayerId,
            'killedPlayerName' => $killledPlayer?->name ?? 'Nobody',
            'killedPlayerColor' => $killledPlayer->activeColor ?? 'gray-500',
            'killedRole' => $killedPlayerRole,
        ]);

        $this->currentRound->completed_at = now();
        $this->currentRound->save();
        event(new GameEnded($this->id));
    }
}
