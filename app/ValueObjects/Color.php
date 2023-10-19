<?php

namespace App\ValueObjects;

enum Color
{
    case red;
    case orange;
    case yellow;
    case lime;
    case emerald;
    case cyan;
    case blue;
    case violet;
    case fuchsia;
    case pink;

    public function baseColor(): string
    {
        return match ($this) {
            self::red => 'red-500',
            self::orange => 'orange-500',
            self::yellow => 'yellow-500',
            self::lime => 'lime-500',
            self::emerald => 'emerald-500',
            self::cyan => 'cyan-500',
            self::blue => 'blue-500',
            self::violet => 'violet-500',
            self::fuchsia => 'fuchsia-500',
            self::pink => 'pink-500',
        };
    }

    public function background(): string
    {
        return 'bg-' . $this->baseColor();
    }

    public function passiveColor(): string
    {
        return match ($this) {
            self::red => 'bg-red-200',
            self::orange => 'bg-orange-200',
            self::yellow => 'bg-yellow-200',
            self::lime => 'bg-lime-200',
            self::emerald => 'bg-emerald-200',
            self::cyan => 'bg-cyan-200',
            self::blue => 'bg-blue-200',
            self::violet => 'bg-violet-200',
            self::fuchsia => 'bg-fuchsia-200',
            self::pink => 'bg-pink-200',
        };
    }

    public static function fromInt(int $int): Color
    {
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
