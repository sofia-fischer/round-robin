<?php

declare(strict_types=1);

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
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
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

    public function humaneReadable(): string
    {
        return match ($this) {
            self::PLANET_X => 'Planet X',
            self::MOON => 'Moon',
            self::GALAXY => 'Galaxy',
            self::EMPTY_SPACE => 'Emptiness',
            self::ASTEROID => 'Asteroid',
            self::PLANET => 'Planet',
        };
    }
}
