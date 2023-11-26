<?php

declare(strict_types=1);

namespace App\ValueObjects;

use App\Http\Livewire\PlanetXComponent;
use App\ValueObjects\Enums\PlanetXIconEnum;
use Iterator;
use Livewire\Form;

class PlanetXBoardForm extends Form implements Iterator
{
    public bool $sector_0_moon = false;
    public bool $sector_0_empty_space = false;
    public bool $sector_0_planet_x = false;
    public bool $sector_0_planet = false;
    public bool $sector_0_galaxy = false;
    public bool $sector_0_asteroid = false;

    public bool $sector_1_moon = false;
    public bool $sector_1_empty_space = false;
    public bool $sector_1_planet_x = false;
    public bool $sector_1_planet = false;
    public bool $sector_1_galaxy = false;
    public bool $sector_1_asteroid = false;

    public bool $sector_2_moon = false;
    public bool $sector_2_empty_space = false;
    public bool $sector_2_planet_x = false;
    public bool $sector_2_planet = false;
    public bool $sector_2_galaxy = false;
    public bool $sector_2_asteroid = false;

    public bool $sector_3_moon = false;
    public bool $sector_3_empty_space = false;
    public bool $sector_3_planet_x = false;
    public bool $sector_3_planet = false;
    public bool $sector_3_galaxy = false;
    public bool $sector_3_asteroid = false;

    public bool $sector_4_moon = false;
    public bool $sector_4_empty_space = false;
    public bool $sector_4_planet_x = false;
    public bool $sector_4_planet = false;
    public bool $sector_4_galaxy = false;
    public bool $sector_4_asteroid = false;

    public bool $sector_5_moon = false;
    public bool $sector_5_empty_space = false;
    public bool $sector_5_planet_x = false;
    public bool $sector_5_planet = false;
    public bool $sector_5_galaxy = false;
    public bool $sector_5_asteroid = false;

    public bool $sector_6_moon = false;
    public bool $sector_6_empty_space = false;
    public bool $sector_6_planet_x = false;
    public bool $sector_6_planet = false;
    public bool $sector_6_galaxy = false;
    public bool $sector_6_asteroid = false;

    public bool $sector_7_moon = false;
    public bool $sector_7_empty_space = false;
    public bool $sector_7_planet_x = false;
    public bool $sector_7_planet = false;
    public bool $sector_7_galaxy = false;
    public bool $sector_7_asteroid = false;

    public bool $sector_8_moon = false;
    public bool $sector_8_empty_space = false;
    public bool $sector_8_planet_x = false;
    public bool $sector_8_planet = false;
    public bool $sector_8_galaxy = false;
    public bool $sector_8_asteroid = false;

    public bool $sector_9_moon = false;
    public bool $sector_9_empty_space = false;
    public bool $sector_9_planet_x = false;
    public bool $sector_9_planet = false;
    public bool $sector_9_galaxy = false;
    public bool $sector_9_asteroid = false;

    public bool $sector_10_moon = false;
    public bool $sector_10_empty_space = false;
    public bool $sector_10_planet_x = false;
    public bool $sector_10_planet = false;
    public bool $sector_10_galaxy = false;
    public bool $sector_10_asteroid = false;

    public bool $sector_11_moon = false;
    public bool $sector_11_empty_space = false;
    public bool $sector_11_planet_x = false;
    public bool $sector_11_planet = false;
    public bool $sector_11_galaxy = false;
    public bool $sector_11_asteroid = false;
    private int $position = 0;

    public function __construct(PlanetXComponent $component, $propertyName)
    {
        parent::__construct($component, $propertyName);
    }

    public function getBoard(): PlanetXBoard
    {
        $board = new PlanetXBoard();

        foreach (range(0, 11) as $sector) {
            foreach (PlanetXIconEnum::cases() as $icon) {
                $propertyName = 'sector_' . $sector . '_' . $icon->value;
                $board->getSector($sector)->setIcon($icon, $this->$propertyName);
            }
        }

        return $board;
    }

    public function current(): PlanetXSector
    {
        $sector = new PlanetXSector();
        foreach (PlanetXIconEnum::cases() as $icon) {
            $propertyName = 'sector_' . $this->position . '_' . $icon->value;
            $sector->setIcon($icon, $this->$propertyName);
        }

        return $sector;
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

    public function setBoard(PlanetXBoard $board): self
    {
        foreach (range(0, 11) as $sector) {
            foreach (PlanetXIconEnum::cases() as $icon) {
                $propertyName = 'sector_' . $sector . '_' . $icon->value;
                $this->$propertyName = $board->getSector($sector)->hasIcon($icon);
            }
        }

        $this->sector_0_moon = false;
        $this->sector_3_moon = false;
        $this->sector_5_moon = false;
        $this->sector_7_moon = false;
        $this->sector_8_moon = false;
        $this->sector_9_moon = false;
        $this->sector_11_moon = false;

        return $this;
    }
}
