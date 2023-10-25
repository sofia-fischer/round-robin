<?php

namespace App\ValueObjects;

use App\ValueObjects\Enums\PlanetXIconEnum;
use Iterator;
use Livewire\Wireable;

class PlanetXBoard implements Wireable, Iterator
{
    private $position = 0;

    public function __construct(
        private PlanetXSector $sector0 = new PlanetXSector(),
        private PlanetXSector $sector1 = new PlanetXSector(),
        private PlanetXSector $sector2 = new PlanetXSector(),
        private PlanetXSector $sector3 = new PlanetXSector(),
        private PlanetXSector $sector4 = new PlanetXSector(),
        private PlanetXSector $sector5 = new PlanetXSector(),
        private PlanetXSector $sector6 = new PlanetXSector(),
        private PlanetXSector $sector7 = new PlanetXSector(),
        private PlanetXSector $sector8 = new PlanetXSector(),
        private PlanetXSector $sector9 = new PlanetXSector(),
        private PlanetXSector $sector10 = new PlanetXSector(),
        private PlanetXSector $sector11 = new PlanetXSector(),
    ) {
    }

    public function getSector(int $index): PlanetXSector
    {
        return match ($index) {
            0 => $this->sector0,
            1 => $this->sector1,
            2 => $this->sector2,
            3 => $this->sector3,
            4 => $this->sector4,
            5 => $this->sector5,
            6 => $this->sector6,
            7 => $this->sector7,
            8 => $this->sector8,
            9 => $this->sector9,
            10 => $this->sector10,
            11 => $this->sector11,
        };
    }

    public function hint(int $section, PlanetXIconEnum $icon)
    {
        $sector = $this->getSector($section);
        $sector->setIcon($icon, ! $sector->hasIcon($icon));
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
        $result = [];

        foreach ($this as $sector) {
            $result[] = $sector->toArray();
        }

        return $result;
    }

    public static function fromArray(array $data): static
    {
        $board = new PlanetXBoard();

        foreach ($board as $index => $sector) {
            foreach (PlanetXIconEnum::cases() as $icon) {
                $sector->setIcon($icon, $data[$index][$icon->value] ?? false);
            }
        }

        return $board;
    }

    public function current(): PlanetXSector
    {
        return $this->getSector($this->position);
    }

    public function next(): void
    {
        $this->position++;
    }

    public function key(): mixed
    {
        return $this->position;
    }

    public function valid(): bool
    {
        return $this->position < 12;
    }

    public function rewind(): void
    {
        $this->position = 0;
    }
}
