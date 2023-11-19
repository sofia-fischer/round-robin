<?php

declare(strict_types=1);

namespace App\ValueObjects;

use App\ValueObjects\Enums\WerewolfRoleEnum;
use App\ValueObjects\Enums\WerewolfStateEnum;
use App\ValueObjects\WerewolfMoves\SeeMove;
use Illuminate\Contracts\Support\Arrayable;
use Livewire\Wireable;

class WerewolfBoard implements Arrayable, Wireable
{
    /**
     * @param  array<string, WerewolfRoleEnum>  $playerRoles
     * @param  \App\ValueObjects\Enums\WerewolfStateEnum  $state
     * @param  array<\App\ValueObjects\WerewolfMoves\SeeMove>  $moves
     */
    public function __construct(
        private array             $playerRoles,
        private WerewolfStateEnum $state = WerewolfStateEnum::NIGHT,
        private array             $moves = [],
    ) {
    }

    /**
     * @param  array<string>  $playerIds
     * @return \App\ValueObjects\WerewolfBoard
     */
    public static function newRoundForPlayers(array $playerIds): WerewolfBoard
    {
        // generating roles
        $playerRoles = [];
        $currentEvilCount = 0;
        $currentWerewolfCount = 0;
        $goalEvilCount = (int) floor(count($playerIds) / 3) ?? 1;

        shuffle($playerIds);
        foreach ($playerIds as $playerId) {
            if ($currentEvilCount < $goalEvilCount) {
                if ($currentWerewolfCount === 0) {
                    $playerRoles[$playerId] = WerewolfRoleEnum::WEREWOLF;
                    $currentWerewolfCount++;
                    continue;
                }

                $evilRoles = [
                    WerewolfRoleEnum::WEREWOLF,
                    WerewolfRoleEnum::WEREWOLF,
                    WerewolfRoleEnum::MINION,
                ];

                $playerRoles[$playerId] = $evilRoles[array_rand($evilRoles)];
                $currentEvilCount++;
                continue;
            }

            $notEvilPossibilities = [
                WerewolfRoleEnum::SEER,
                WerewolfRoleEnum::VILLAGER,
                WerewolfRoleEnum::VILLAGER,
                WerewolfRoleEnum::TANNER,
            ];
            $playerRoles[$playerId] = $notEvilPossibilities[array_rand($notEvilPossibilities)];
        }

        return new WerewolfBoard($playerRoles);
    }

    public function toArray(): array
    {
        $roles = [];

        foreach ($this->playerRoles as $playerId => $role) {
            $roles[$playerId] = $role->value;
        }

        $moves = [];
        foreach ($this->moves as $move) {
            $moves[] = $move->toArray();
        }

        return [
            'playerRoles' => $roles,
            'state' => $this->state->value,
            'moves' => $moves,
        ];
    }

    public static function fromArray(array $array): WerewolfBoard
    {
        $roles = [];
        foreach ($array['playerRoles'] as $playerId => $role) {
            $roles[$playerId] = WerewolfRoleEnum::from($role);
        }

        $moves = [];
        foreach ($array['moves'] as $move) {
            $moves[] = SeeMove::fromArray($move);
        }

        return new WerewolfBoard(
            playerRoles: $roles,
            state: WerewolfStateEnum::from($array['state']),
            moves: $moves,
        );
    }

    public function countRole(WerewolfRoleEnum $needleRole): int
    {
        return count(array_filter($this->playerRoles, fn ($role) => $role === $needleRole));
    }

    public function see(string $identifier): WerewolfRoleEnum|null
    {
        return $this->playerRoles[$identifier] ?? null;
    }

    public function canSee(string $identifier, string $wantsToSee): bool
    {
        if ($this->see($identifier) === WerewolfRoleEnum::WEREWOLF && $this->see($wantsToSee) === WerewolfRoleEnum::WEREWOLF) {
            return true;
        }

        if ($this->see($identifier) === WerewolfRoleEnum::MINION && $this->see($wantsToSee) === WerewolfRoleEnum::WEREWOLF) {
            return true;
        }

        foreach ($this->moves as $move) {
            if ($move->watcherId === $identifier && $move->seeId === $wantsToSee) {
                return true;
            }
        }

        return false;
    }

    public function canMakeSeeMove(string $identifier, string $wantToSee): bool
    {
        if ($this->see($identifier) !== WerewolfRoleEnum::SEER) {
            return false;
        }
        foreach ($this->moves as $move) {
            if ($move->watcherId === $identifier) {
                return false;
            }
        }

        return true;
    }

    public function makeMove(SeeMove $move)
    {
        $this->moves[] = $move;
    }

    public function identifiersWithRole(WerewolfRoleEnum $needleRole): array
    {
        return array_filter($this->playerRoles, fn ($role) => $role === $needleRole);
    }

    public function getState(): WerewolfStateEnum
    {
        return $this->state;
    }

    public function isDay(): bool
    {
        return $this->state === WerewolfStateEnum::DAY;
    }

    public function isNight(): bool
    {
        return $this->state === WerewolfStateEnum::NIGHT;
    }

    public function isEnd(): bool
    {
        return $this->state === WerewolfStateEnum::END;
    }

    public function setState(WerewolfStateEnum $state): void
    {
        $this->state = $state;
    }

    public function toLivewire(): array
    {
        return $this->toArray();
    }

    public static function fromLivewire($value): static
    {
        return self::fromArray($value);
    }
}
