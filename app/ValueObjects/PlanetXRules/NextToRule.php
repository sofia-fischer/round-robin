<?php

declare(strict_types=1);

namespace App\ValueObjects\PlanetXRules;

use App\ValueObjects\Enums\PlanetXIconEnum;
use App\ValueObjects\PlanetXBoard;

class NextToRule extends PlanetXRule
{
    public function __construct(
        public readonly PlanetXIconEnum $icon,
        public readonly PlanetXIconEnum $mustBeNextToIcon,
    ) {
    }

    public function isValid(PlanetXBoard $board): bool
    {
        foreach ($board as $index => $sector) {
            if (! $sector->hasIcon($this->icon)) {
                continue;
            }

            if (! $board->getSector(($index + 11) % 12)->hasIcon($this->mustBeNextToIcon)
                && ! $board->getSector(($index + 1) % 12)->hasIcon($this->mustBeNextToIcon)) {
                $this->errorMessage = "Sector " . $index + 1 . " does not have " .
                    $this->icon->value . " next to a " . $this->mustBeNextToIcon->value;

                return false;
            }
        }

        return true;
    }

    public function toArray(): array
    {
        return [
            'type' => self::class,
            'icon' => $this->icon->value,
            'mustBeNextToIcon' => $this->mustBeNextToIcon->value,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            icon: PlanetXIconEnum::from($data['icon']),
            mustBeNextToIcon: PlanetXIconEnum::from($data['mustBeNextToIcon']),
        );
    }
}
