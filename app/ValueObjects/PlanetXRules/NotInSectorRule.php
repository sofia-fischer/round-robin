<?php

declare(strict_types=1);

namespace App\ValueObjects\PlanetXRules;

use App\ValueObjects\Enums\PlanetXIconEnum;
use App\ValueObjects\PlanetXBoard;

class NotInSectorRule extends PlanetXRule
{
    public function __construct(
        public PlanetXIconEnum $icon,
        public int             $sector
    ) {
    }

    public function isValid(PlanetXBoard $board): bool
    {
        if ($board->getSector($this->sector)->hasIcon($this->icon)) {
            $this->errorMessage = "There is a {$this->icon->value} in sector " . ($this->sector + 1) . ".";

            return false;
        };

        return true;
    }

    public function toArray(): array
    {
        return [
            'type' => self::class,
            'icon' => $this->icon->value,
            'sector' => $this->sector,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            icon: PlanetXIconEnum::from($data['icon']),
            sector: $data['sector'],
        );
    }
}
