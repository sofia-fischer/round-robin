<?php

declare(strict_types=1);

namespace App\ValueObjects\PlanetXRules;

use App\ValueObjects\Enums\PlanetXIconEnum;
use App\ValueObjects\PlanetXBoard;

class NotNextToRule extends PlanetXRule
{
    public function __construct(
        private PlanetXIconEnum $icon,
        private PlanetXIconEnum $mustNotBeNextToIcon,
    )
    {
    }

    public function isValid(PlanetXBoard $board): bool
    {
        foreach ($board as $index => $sector) {
            if (! $sector->hasIcon($this->icon)) {
                continue;
            }

            $previousSector = $board->getSector(($index - 1) < 0 ? 11 : ($index - 1));
            $nextSector = $board->getSector(($index + 1) % 12);

            if ($previousSector->hasIcon($this->mustNotBeNextToIcon)
                || $nextSector->hasIcon($this->mustNotBeNextToIcon)) {
                return false;
            }
        }

        return true;
    }
}
