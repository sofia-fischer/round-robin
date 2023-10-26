<?php

declare(strict_types=1);

namespace App\ValueObjects\PlanetXRules;

use App\ValueObjects\Enums\PlanetXIconEnum;
use App\ValueObjects\PlanetXBoard;

class NextToRule extends PlanetXRule
{
    public function __construct(
        private PlanetXIconEnum $icon,
        private PlanetXIconEnum $mustBeNextToIcon,
    ) {
    }

    public function isValid(PlanetXBoard $board): bool
    {
        foreach ($board as $index => $sector) {
            if (! $sector->hasIcon($this->icon)) {
                continue;
            }

            if (! $board->getSector(($index + 11) % 12)->hasIcon($this->mustBeNextToIcon)
                && ! $board->getSector(($index + 1) % 12)->hasIcon($this->mustBeNextToIcon)) {
                $this->errorMessage = "Sector " . $index + 1 . " does not have " .
                    $this->icon->value . " next to " . $this->mustBeNextToIcon->value;

                return false;
            }
        }

        return true;
    }
}
