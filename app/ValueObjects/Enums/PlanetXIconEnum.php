<?php

namespace App\ValueObjects\Enums;

enum PlanetXIconEnum: string
{
    case  PLANET_X = 'planet_x';
    case  EMPTY_SPACE = 'empty_space';
    case  GALAXY = 'galaxy';
    case  PLANET = 'planet';
    case  ASTEROID = 'asteroid';
    case  MOON = 'moon';

    /**
     * @return array<string>
     */
    public static function  values(): array
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

    public static function diff(array $icons, array $other = null): array
    {
        if ($other === null) {
            $other = self::cases();
        }

        $iconValues = array_map(fn ($icon) => $icon->value, $icons);
        $otherIconValues = array_map(fn ($icon) => $icon->value, $other);
        $diff = array_diff($otherIconValues, $iconValues);

        return array_map(fn ($icon) => self::from($icon), $diff);
    }
}
