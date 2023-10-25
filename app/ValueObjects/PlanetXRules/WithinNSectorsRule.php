<?php

declare(strict_types=1);

namespace App\ValueObjects\PlanetXRules;

use App\ValueObjects\Enums\PlanetXIconEnum;
use App\ValueObjects\PlanetXBoard;

class WithinNSectorsRule extends PlanetXRule
{
    public function __construct(
        private PlanetXIconEnum $icon,
        private int             $within,
        private PlanetXIconEnum $otherIcon,
    ) {
    }

    public function isValid(PlanetXBoard $board): bool
    {
        foreach ($board as $index => $sector) {
            if (! $sector->hasIcon($this->icon)) {
                continue;
            }

            for ($countWithin = 1; $countWithin <= $this->within; $countWithin++) {
                $nextIndex = ($index + $countWithin) % 12;
                if ($board->getSector($nextIndex)->hasIcon($this->otherIcon)) {
                    continue 2;
                }

                $previousIndex = ($index - $countWithin) < 0 ? (12 + ($index - $countWithin)) : ($index - $countWithin);
                if ($board->getSector($previousIndex)->hasIcon($this->otherIcon)) {
                    continue 2;
                }
            }

            return false;
        }

        return true;
    }
}
