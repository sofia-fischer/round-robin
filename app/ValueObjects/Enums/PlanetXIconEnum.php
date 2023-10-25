<?php

namespace App\ValueObjects\Enums;

enum PlanetXIconEnum: string
{
    case  PLANET_X = 'planet_x';
    case  MOON = 'moon';
    case  GALAXY = 'galaxy';
    case  EMPTY_SPACE = 'empty_space';
    case  COMET = 'comet';
    case  PLANET = 'planet';

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
