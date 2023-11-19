<?php

declare(strict_types=1);

namespace App\ValueObjects\Enums;

enum WerewolfStateEnum: string
{
    case  NIGHT = 'night';
    case  DAY = 'day';
    case  END = 'end';

    public function gradient(): string
    {
        return match ($this) {
            self::NIGHT => 'from-blue-900 to-black text-white',
            self::DAY => 'from-blue-300 via-pink-200 to-yellow-100 text-indigo-900',
            self::END => 'from-yellow-400 via-red-800 to-black text-white',
        };
    }
}
