<?php

declare(strict_types=1);

namespace App\ValueObjects\PlanetXRules;

use App\ValueObjects\Enums\PlanetXIconEnum;
use App\ValueObjects\PlanetXBoard;

class NotInSectorRule extends PlanetXRule
{
    public function __construct(
        private PlanetXIconEnum $icon,
        private int             $sector
    ) {
    }

    public function isValid(PlanetXBoard $board): bool
    {
        if ($board->getSector($this->sector)->hasIcon($this->icon)) {
            $this->errorMessage = "There is a {$this->icon->value} in sector " . ($this->sector + 1) . ".";

            return false;
        };

        return true;
    }
}
