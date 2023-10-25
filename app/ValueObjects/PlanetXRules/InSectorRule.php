<?php

declare(strict_types=1);

namespace App\ValueObjects\PlanetXRules;

use App\ValueObjects\Enums\PlanetXIconEnum;
use App\ValueObjects\PlanetXBoard;

class InSectorRule extends PlanetXRule
{
    public function __construct(
        private PlanetXIconEnum $icon,
        private int    $sector
    ) {
    }

    public function isValid(PlanetXBoard $board): bool
    {
        return $board->getSector($this->sector)->hasIcon($this->icon);
    }
}
