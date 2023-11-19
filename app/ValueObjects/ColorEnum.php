<?php

declare(strict_types=1);

namespace App\ValueObjects;

use Ramsey\Uuid\Uuid;

enum ColorEnum: string
{
    case stone = 'stone';
    case red = 'red';
    case orange = 'orange';
    case yellow = 'yellow';
    case lime = 'lime';
    case emerald = 'emerald';
    case cyan = 'cyan';
    case blue = 'blue';
    case violet = 'violet';
    case fuchsia = 'fuchsia';
    case pink = 'pink';

    public static function nameFromUuid(string $id)
    {
        $int = substr(Uuid::fromString($id)->getInteger()->toString(), -2);
        $modulo = $int % 20;

        $animal = match ($modulo) {
            0 => 'Ferret',
            1 => 'Dog',
            2 => 'Axolotl',
            3 => 'Ozelot',
            4 => 'Fox',
            19 => 'Lama',
            5 => 'Sheep',
            6 => 'Zebra',
            9 => 'Donkey',
            12 => 'Rabbit',
            14 => 'Moth',
            7 => 'Platypus',
            8 => 'Frog',
            10 => 'Sloth',
            11 => 'Unicorn',
            13 => 'Wolf',
            15 => 'Koala',
            16 => 'Python',
            17 => 'Cougar',
            18 => 'Camel',
        };

        $color = self::fromUuid($id)->value;

        return $color . ' ' . $animal;
    }

    public function baseColor(): string
    {
        return $this->value . '-500';
    }

    public function background(): string
    {
        return 'bg-' . $this->baseColor();
    }

    public function passiveColor(): string
    {
        return $this->value . '-200';
    }

    public static function tryFromUuid(string $id): ?ColorEnum
    {
        try {
            return self::fromUuid($id);
        } catch (\Throwable) {
            return null;
        }
    }

    public static function fromUuid(string $id): ColorEnum
    {
        $int = substr(Uuid::fromString($id)->getInteger()->toString(), -2);
        $modulo = $int % 10;

        return match ($modulo) {
            0 => self::red,
            1 => self::orange,
            2 => self::yellow,
            3 => self::lime,
            4 => self::emerald,
            5 => self::cyan,
            6 => self::blue,
            7 => self::violet,
            8 => self::fuchsia,
            9 => self::pink,
        };
    }
}
