<?php

namespace App\ValueObjects\PlanetXRules;

use App\Models\PlanetXGame;
use Illuminate\Support\Arr;
use Livewire\Wireable;

class PlanetXBoard implements Wireable
{
    public function __construct(
        private PlanetXSector $sector1,
        private PlanetXSector $sector2,
        private PlanetXSector $sector3,
        private PlanetXSector $sector4,
        private PlanetXSector $sector5,
        private PlanetXSector $sector6,
        private PlanetXSector $sector7,
        private PlanetXSector $sector8,
        private PlanetXSector $sector9,
        private PlanetXSector $sector10,
        private PlanetXSector $sector11,
        private PlanetXSector $sector12,
    ) {
    }

    /**
     * @return array<PlanetXSector>
     */
    public function getLeftSectors(): array
    {
        return [
            1 => $this->sector1->withMargin('mr-2'),
            2 => $this->sector2->withMargin('mr-6'),
            3 => $this->sector3->withMargin('mr-16'),
            4 => $this->sector4->withMargin('mr-16'),
            5 => $this->sector5->withMargin('mr-6'),
            6 => $this->sector6->withMargin('mr-2'),
        ];
    }

    /**
     * @return array<PlanetXSector>
     */
    public function getRightSectors(): array
    {
        return [
            7 => $this->sector7->withMargin('ml-2'),
            8 => $this->sector8->withMargin('ml-6'),
            9 => $this->sector9->withMargin('ml-16'),
            10 => $this->sector10->withMargin('ml-16'),
            11 => $this->sector11->withMargin('ml-6'),
            12 => $this->sector12->withMargin('ml-2'),
        ];
    }

    public function hint(int $section, string $icon)
    {
        match ($section) {
            1 => $this->sector1->hint($icon),
            2 => $this->sector2->hint($icon),
            3 => $this->sector3->hint($icon),
            4 => $this->sector4->hint($icon),
            5 => $this->sector5->hint($icon),
            6 => $this->sector6->hint($icon),
            7 => $this->sector7->hint($icon),
            8 => $this->sector8->hint($icon),
            9 => $this->sector9->hint($icon),
            10 => $this->sector10->hint($icon),
            11 => $this->sector11->hint($icon),
            12 => $this->sector12->hint($icon),
        };
    }

    public function toLivewire(): array
    {
        return $this->toArray();
    }

    public static function fromLivewire($value): static
    {
        return self::fromArray($value);
    }

    public function toArray(): array
    {
        return [
            1 => $this->sector1->getIcons(),
            2 => $this->sector2->getIcons(),
            3 => $this->sector3->getIcons(),
            4 => $this->sector4->getIcons(),
            5 => $this->sector5->getIcons(),
            6 => $this->sector6->getIcons(),
            7 => $this->sector7->getIcons(),
            8 => $this->sector8->getIcons(),
            9 => $this->sector9->getIcons(),
            10 => $this->sector10->getIcons(),
            11 => $this->sector11->getIcons(),
            12 => $this->sector12->getIcons(),
        ];
    }

    public static function fromArray(array $data): static
    {
        $sectors = Arr::mapWithKeys([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12], function ($sector) use ($data) {
            return [$sector => new PlanetXSector(
                moon: $data[$sector][PlanetXGame::MOON] ?? false,
                emptySpace: $data[$sector][PlanetXGame::EMPTY_SPACE] ?? false,
                planetX: $data[$sector][PlanetXGame::PLANET_X] ?? false,
                planet: $data[$sector][PlanetXGame::PLANET] ?? false,
                galaxy: $data[$sector][PlanetXGame::GALAXY] ?? false,
                comet: $data[$sector][PlanetXGame::COMET] ?? false,
            )];
        });

        return new PlanetXBoard(
            $sectors[1],
            $sectors[2],
            $sectors[3],
            $sectors[4],
            $sectors[5],
            $sectors[6],
            $sectors[7],
            $sectors[8],
            $sectors[9],
            $sectors[10],
            $sectors[11],
            $sectors[12],
        );
    }
}
