<?php

namespace App\ValueObjects\PlanetXRules;

use App\Models\PlanetXGame;

class PlanetXSector
{
    public string $margin;

    public function __construct(
        public bool $moon = false,
        public bool $emptySpace = false,
        public bool $planetX = false,
        public bool $planet = false,
        public bool $galaxy = false,
        public bool $comet = false,
    ) {
    }

    public static function allTrue(): self
    {
        return new self(
            moon: true,
            emptySpace: true,
            planetX: true,
            planet: true,
            galaxy: true,
            comet: true,
        );
    }

    public function withMargin(string $margin): self
    {
        $this->margin = $margin;

        return $this;
    }

    /**
     * @return array<string, bool>
     */
    public function getIcons(): array
    {
        return [
            PlanetXGame::MOON,
            PlanetXGame::EMPTY_SPACE,
            PlanetXGame::PLANET_X,
            PlanetXGame::PLANET,
            PlanetXGame::GALAXY,
            PlanetXGame::COMET,
        ];
    }

    public function hasIcon(string $iconName): bool
    {
        return match ($iconName) {
            PlanetXGame::PLANET => $this->planet,
            PlanetXGame::PLANET_X => $this->planetX,
            PlanetXGame::COMET => $this->comet,
            PlanetXGame::GALAXY => $this->galaxy,
            PlanetXGame::MOON => $this->moon,
            PlanetXGame::EMPTY_SPACE => $this->emptySpace,
        };
    }

    public function hint(string $icon)
    {
        match ($icon) {
            PlanetXGame::PLANET => $this->planet = ! $this->hasIcon($icon),
            PlanetXGame::PLANET_X => $this->planetX = ! $this->hasIcon($icon),
            PlanetXGame::COMET => $this->comet = ! $this->hasIcon($icon),
            PlanetXGame::GALAXY => $this->galaxy = ! $this->hasIcon($icon),
            PlanetXGame::MOON => $this->moon = ! $this->hasIcon($icon),
            PlanetXGame::EMPTY_SPACE => $this->emptySpace = ! $this->hasIcon($icon),
        };
    }
}
