<?php

namespace App\ValueObjects;

use App\ValueObjects\Enums\PlanetXIconEnum;
use App\ValueObjects\PlanetXRules\NextToRule;
use App\ValueObjects\PlanetXRules\NotInSectorRule;
use App\ValueObjects\PlanetXRules\NotNextToRule;
use App\ValueObjects\PlanetXRules\PlanetXRule;
use Illuminate\Contracts\Support\Arrayable;
use Iterator;
use Livewire\Wireable;

class PlanetXBoard implements Wireable, Iterator, Arrayable
{
    private $position = 0;

    private PlanetXSector $sector0;
    private PlanetXSector $sector1;
    private PlanetXSector $sector2;
    private PlanetXSector $sector3;
    private PlanetXSector $sector4;
    private PlanetXSector $sector5;
    private PlanetXSector $sector6;
    private PlanetXSector $sector7;
    private PlanetXSector $sector8;
    private PlanetXSector $sector9;
    private PlanetXSector $sector10;
    private PlanetXSector $sector11;

    public function __construct()
    {
        $this->sector0 = new PlanetXSector();
        $this->sector1 = new PlanetXSector();
        $this->sector2 = new PlanetXSector();
        $this->sector3 = new PlanetXSector();
        $this->sector4 = new PlanetXSector();
        $this->sector5 = new PlanetXSector();
        $this->sector6 = new PlanetXSector();
        $this->sector7 = new PlanetXSector();
        $this->sector8 = new PlanetXSector();
        $this->sector9 = new PlanetXSector();
        $this->sector10 = new PlanetXSector();
        $this->sector11 = new PlanetXSector();
    }

    public static function playerBoard(): PlanetXBoard
    {
        return PlanetXBoard::fromArray([
            [PlanetXIconEnum::values()],
            [PlanetXIconEnum::values()],
            [PlanetXIconEnum::values()],
            [PlanetXIconEnum::values()],
            [PlanetXIconEnum::values()],
            [PlanetXIconEnum::values()],
            [PlanetXIconEnum::values()],
            [PlanetXIconEnum::values()],
            [PlanetXIconEnum::values()],
            [PlanetXIconEnum::values()],
            [PlanetXIconEnum::values()],
            [PlanetXIconEnum::values()],
        ]);
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
                $sector->setIcon($icon, in_array($icon->value, $data[$index] ?? []));
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

    public function getSingleSectorIndexesWithoutIcon(): array
    {
        return array_values(array_filter(
            $this->getSectorIndexesWithIcon(),
            fn ($index) => ($this->hasAnyIcon(($index + 1) % 12) && $this->hasAnyIcon(($index + 11) % 12))
        ));
    }

    public function hasAnyIcon(int $index): bool
    {
        return count($this->getSector($index)->toArray()) !== 0;
    }

    /**
     * @return array<PlanetXRule>
     */
    public static function getStartingRules(): array
    {
        return [
            new NotInSectorRule(PlanetXIconEnum::MOON, 0),
            new NotInSectorRule(PlanetXIconEnum::MOON, 3),
            new NotInSectorRule(PlanetXIconEnum::MOON, 5),
            new NotInSectorRule(PlanetXIconEnum::MOON, 7),
            new NotInSectorRule(PlanetXIconEnum::MOON, 8),
            new NotInSectorRule(PlanetXIconEnum::MOON, 9),
            new NotInSectorRule(PlanetXIconEnum::MOON, 11),
            new NextToRule(PlanetXIconEnum::ASTEROID, PlanetXIconEnum::ASTEROID),
            new NotNextToRule(PlanetXIconEnum::PLANET, PlanetXIconEnum::PLANET_X),
            new NextToRule(PlanetXIconEnum::GALAXY, PlanetXIconEnum::EMPTY_SPACE),
        ];
    }

    /**
     * @param  PlanetXIconEnum|null|array<PlanetXIconEnum>  $icon
     * @return array<int>
     */
    public function getSectorIndexesWithIcon(PlanetXIconEnum|array $icon = []): array
    {
        $icons = is_array($icon) ? $icon : [$icon];
        $sectors = [];

        foreach ($this as $index => $sector) {
            if ($sector->hasIcon($icons)) {
                $sectors[] = $index;
            }
        }

        return $sectors;
    }
}
