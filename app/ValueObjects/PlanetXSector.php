<?php

namespace App\ValueObjects;

use App\Exceptions\PlanetXBoardGenerationException;
use App\ValueObjects\Enums\PlanetXIconEnum;
use Illuminate\Contracts\Support\Arrayable;

class PlanetXSector implements Arrayable
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

    /**
     * @param  \App\ValueObjects\Enums\PlanetXIconEnum|array<PlanetXIconEnum>  $iconName
     * @return bool
     */
    public function hasIcon(PlanetXIconEnum|array $icons): bool
    {
        $icons = is_array($icons) ? $icons : [$icons];
        foreach ($icons as $icon) {
            $hasIcon = match ($icon) {
                PlanetXIconEnum::PLANET => $this->planet,
                PlanetXIconEnum::PLANET_X => $this->planetX,
                PlanetXIconEnum::ASTEROID => $this->asteroid,
                PlanetXIconEnum::GALAXY => $this->galaxy,
                PlanetXIconEnum::MOON => $this->moon,
                PlanetXIconEnum::EMPTY_SPACE => $this->emptySpace,
            };

            if ($hasIcon) {
                return true;
            }
        }
        return false;
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

    public function getIcon()
    {
        return match (true) {
            $this->planet => PlanetXIconEnum::PLANET,
            $this->planetX => PlanetXIconEnum::PLANET_X,
            $this->asteroid => PlanetXIconEnum::ASTEROID,
            $this->galaxy => PlanetXIconEnum::GALAXY,
            $this->moon => PlanetXIconEnum::MOON,
            $this->emptySpace => PlanetXIconEnum::EMPTY_SPACE,
        };
    }
}
