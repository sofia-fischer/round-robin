<?php

declare(strict_types=1);

namespace App\ValueObjects\PlanetXRules;

use App\ValueObjects\PlanetXBoard;

abstract class PlanetXRule
{
    protected string $errorMessage = '';

    abstract public function isValid(PlanetXBoard $board): bool;

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}
