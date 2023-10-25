<?php

declare(strict_types=1);

namespace App\ValueObjects\PlanetXRules;

use App\ValueObjects\Enums\PlanetXIconEnum;
use App\ValueObjects\PlanetXBoard;

class InABandOfNSectorsRule extends PlanetXRule
{
    public function __construct(
        private PlanetXIconEnum $icon,
        private int    $within,
    ) {
    }

    public function isValid(PlanetXBoard $board): bool
    {
        // the board is valid if all icons are within n consecutive sectors.
        // therefor the board is valid, if there exists a band of 12-n sectors, which do not contain the icon.
        $numberOfSectorsWithoutTheIcon = 12 - $this->within;

        foreach ($board as $index => $sector) {
            if ($sector->hasIcon($this->icon)) {
                continue;
            }

            for ($countWithoutIcon = 1; $countWithoutIcon < $numberOfSectorsWithoutTheIcon; $countWithoutIcon++) {
                if ($board->getSector(($index + $countWithoutIcon) % 12)->hasIcon($this->icon)) {
                    continue 2;
                }
            }

            // if we reach this point, we have found a band of 12-n sectors, which do not contain the icon.
            return true;
        }

        return false;
    }
}
