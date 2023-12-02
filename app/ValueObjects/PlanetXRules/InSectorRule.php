<?php

declare(strict_types=1);

namespace App\ValueObjects\PlanetXRules;

use App\ValueObjects\Enums\PlanetXIconEnum;
use App\ValueObjects\PlanetXBoard;

class InSectorRule extends PlanetXRule
{
    public function __construct(
        public readonly PlanetXIconEnum $icon,
        public readonly int             $sector
    ) {
    }

    public function isValid(PlanetXBoard $board): bool
    {
        if ($board->getSector($this->sector)->hasIcon($this->icon)) {
            return true;
        };

        if ($this->icon === PlanetXIconEnum::EMPTY_SPACE && $board->getSector($this->sector)->hasIcon(PlanetXIconEnum::PLANET_X)) {
            return true;
        }

        $this->errorMessage = "Sector " . $this->sector + 1 . " does not have " . $this->icon->value;

        return false;
    }

    public function toArray(): array
    {
        return [
            'type' => self::class,
            'icon' => $this->icon->value,
            'sector' => $this->sector,];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            icon: PlanetXIconEnum::from($data['icon']),
            sector: $data['sector'],
        );
    }
}
