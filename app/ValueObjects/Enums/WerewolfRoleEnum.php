<?php

declare(strict_types=1);

namespace App\ValueObjects\Enums;

enum WerewolfRoleEnum: string
{
    case  WEREWOLF = 'werewolf';
    case  MASON = 'mason';
    case  MINION = 'minion';
    case  SEER = 'seer';
    case  VILLAGER = 'villager';
    case  TANNER = 'tanner';

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
