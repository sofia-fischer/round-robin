<?php

declare(strict_types=1);

namespace App\ValueObjects\PlanetXRules;

use App\ValueObjects\Enums\PlanetXIconEnum;
use App\ValueObjects\PlanetXBoard;

class NotNextToRule extends PlanetXRule
{
    public function __construct(
        public readonly PlanetXIconEnum $icon,
        public readonly PlanetXIconEnum $mustNotBeNextToIcon,
    ) {
    }

    public function isValid(PlanetXBoard $board): bool
    {
        foreach ($board as $index => $sector) {
            if (! $sector->hasIcon($this->icon)) {
                continue;
            }

            $previousSector = $board->getSector(($index - 1) < 0 ? 11 : ($index - 1));
            $nextSector = $board->getSector(($index + 1) % 12);

            if ($previousSector->hasIcon($this->mustNotBeNextToIcon)
                || $nextSector->hasIcon($this->mustNotBeNextToIcon)) {
                $this->errorMessage = "Sector " . ($index + 1) . " does have " . $this->icon->value
                    . " next to " . $this->mustNotBeNextToIcon->value;

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
            'mustNotBeNextToIcon' => $this->mustNotBeNextToIcon->value,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            icon: PlanetXIconEnum::from($data['icon']),
            mustNotBeNextToIcon: PlanetXIconEnum::from($data['mustNotBeNextToIcon']),
        );
    }
}
