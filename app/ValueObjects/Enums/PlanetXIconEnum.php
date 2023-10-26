<?php

namespace App\ValueObjects\Enums;

enum PlanetXIconEnum: string
{
    case  PLANET_X = 'planet_x';
    case  MOON = 'moon';
    case  GALAXY = 'galaxy';
    case  EMPTY_SPACE = 'empty_space';
    case  ASTEROID = 'asteroid';
    case  PLANET = 'planet';

    /**
     * @return array<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function appearsAs(): PlanetXIconEnum
    {
        return match ($this) {
            self::PLANET_X => self::EMPTY_SPACE,
            self::MOON => self::MOON,
            self::GALAXY => self::GALAXY,
            self::EMPTY_SPACE => self::EMPTY_SPACE,
            self::ASTEROID => self::ASTEROID,
            self::PLANET => self::PLANET,
        };
    }
}
