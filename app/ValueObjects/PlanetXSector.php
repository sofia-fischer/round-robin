<?php

namespace App\ValueObjects;

use App\ValueObjects\Enums\PlanetXIconEnum;

class PlanetXSector
{
    public function __construct(
        public bool $moon = false,
        public bool $emptySpace = false,
        public bool $planetX = false,
        public bool $planet = false,
        public bool $galaxy = false,
        public bool $comet = false,
    ) {
    }

    /**
     * @return array<string, bool>
     */
    public function toArray(): array
    {
        return [
            PlanetXIconEnum::PLANET->value => $this->planet,
            PlanetXIconEnum::PLANET_X->value => $this->planetX,
            PlanetXIconEnum::COMET->value => $this->comet,
            PlanetXIconEnum::GALAXY->value => $this->galaxy,
            PlanetXIconEnum::MOON->value => $this->moon,
            PlanetXIconEnum::EMPTY_SPACE->value => $this->emptySpace,
        ];
    }

    public function hasIcon(PlanetXIconEnum $iconName): bool
    {
        return match ($iconName) {
            PlanetXIconEnum::PLANET => $this->planet,
            PlanetXIconEnum::PLANET_X => $this->planetX,
            PlanetXIconEnum::COMET => $this->comet,
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
            PlanetXIconEnum::COMET => $this->comet = $value,
            PlanetXIconEnum::GALAXY => $this->galaxy = $value,
            PlanetXIconEnum::MOON => $this->moon = $value,
            PlanetXIconEnum::EMPTY_SPACE => $this->emptySpace = $value,
        };
    }
}
