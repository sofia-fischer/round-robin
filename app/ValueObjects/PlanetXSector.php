<?php

namespace App\ValueObjects;

use App\Exceptions\PlanetXBoardGenerationException;
use App\ValueObjects\Enums\PlanetXIconEnum;

class PlanetXSector
{
    public function __construct(
        public bool $moon = false,
        public bool $emptySpace = false,
        public bool $planetX = false,
        public bool $planet = false,
        public bool $galaxy = false,
        public bool $asteroid = false,
    ) {
    }

    /**
     * @return array<string>
     */
    public function toArray(): array
    {
        return array_keys(array_filter([
            PlanetXIconEnum::PLANET->value => $this->planet,
            PlanetXIconEnum::PLANET_X->value => $this->planetX,
            PlanetXIconEnum::ASTEROID->value => $this->asteroid,
            PlanetXIconEnum::GALAXY->value => $this->galaxy,
            PlanetXIconEnum::MOON->value => $this->moon,
            PlanetXIconEnum::EMPTY_SPACE->value => $this->emptySpace,
        ]));
    }

    public function hasIcon(PlanetXIconEnum $iconName): bool
    {
        return match ($iconName) {
            PlanetXIconEnum::PLANET => $this->planet,
            PlanetXIconEnum::PLANET_X => $this->planetX,
            PlanetXIconEnum::ASTEROID => $this->asteroid,
            PlanetXIconEnum::GALAXY => $this->galaxy,
            PlanetXIconEnum::MOON => $this->moon,
            PlanetXIconEnum::EMPTY_SPACE => $this->emptySpace,
        };
    }

    public function setIcon(PlanetXIconEnum $iconName, bool $value): bool
    {
        return match ($iconName) {
            PlanetXIconEnum::PLANET => $this->planet = $value,
            PlanetXIconEnum::PLANET_X => $this->planetX = $value,
            PlanetXIconEnum::ASTEROID => $this->asteroid = $value,
            PlanetXIconEnum::GALAXY => $this->galaxy = $value,
            PlanetXIconEnum::MOON => $this->moon = $value,
            PlanetXIconEnum::EMPTY_SPACE => $this->emptySpace = $value,
        };
    }

    public function setIconOrFail(PlanetXIconEnum $iconName, bool $value): bool
    {
        if (count($this->toArray()) > 0) {
            throw new PlanetXBoardGenerationException("Tried to set {$iconName->value}", $this->toArray());
        }

        return match ($iconName) {
            PlanetXIconEnum::PLANET => $this->planet = $value,
            PlanetXIconEnum::PLANET_X => $this->planetX = $value,
            PlanetXIconEnum::ASTEROID => $this->asteroid = $value,
            PlanetXIconEnum::GALAXY => $this->galaxy = $value,
            PlanetXIconEnum::MOON => $this->moon = $value,
            PlanetXIconEnum::EMPTY_SPACE => $this->emptySpace = $value,
        };
    }
}
