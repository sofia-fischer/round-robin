<?php

namespace App\Models;

use Illuminate\Support\Collection;
use App\Support\GameLogics\JustOneLogic;
use App\Support\GameLogics\WavelengthLogic;
use App\Support\GameLogics\OneNightWerewolfLogic;
use LEVELS\Analytics\Tracking\Queue\Events\CalculationQueued;

/**
 * Class GameLogic
 *
 * @package app/Database/Models
 */
class GameLogic
{
    const WAVELENGTH         = WavelengthLogic::class;
    const ONE_NIGHT_WEREWOLF = OneNightWerewolfLogic::class;
    const JUST_ONE           = JustOneLogic::class;

    static function get(): Collection
    {
        return collect([
            self::WAVELENGTH,
            self::ONE_NIGHT_WEREWOLF,
            self::JUST_ONE,
        ]);
    }

    static function validationString(): string
    {
        return implode(',', self::get()->toArray());
    }
}
