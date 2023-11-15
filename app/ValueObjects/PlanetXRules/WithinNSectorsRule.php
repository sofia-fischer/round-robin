<?php

declare(strict_types=1);

namespace App\ValueObjects\PlanetXRules;

use App\ValueObjects\Enums\PlanetXIconEnum;
use App\ValueObjects\PlanetXBoard;

class WithinNSectorsRule extends PlanetXRule
{
    public function __construct(
        public readonly PlanetXIconEnum $icon,
        public readonly int             $within,
        public readonly PlanetXIconEnum $otherIcon,
    ) {
    }

    public function isValid(PlanetXBoard $board): bool
    {
        foreach ($board as $index => $sector) {
            if (! $sector->hasIcon($this->icon)) {
                continue;
            }

            for ($countWithin = 1; $countWithin <= $this->within; $countWithin++) {
                $nextIndex = ($index + $countWithin) % 12;
                if ($board->getSector($nextIndex)->hasIcon($this->otherIcon)) {
                    continue 2;
                }

                $previousIndex = ($index + (12 - $countWithin)) % 12;
                if ($board->getSector($previousIndex)->hasIcon($this->otherIcon)) {
                    continue 2;
                }
            }

            $this->errorMessage = "Sector " . ($index + 1) . " does not have " . $this->icon->value
                . " within " . $this->within . " sectors of " . $this->otherIcon->value;

            return false;
        }

        return true;
    }

    public function equals(PlanetXRule $rule): bool
    {
        if (! $rule instanceof self) {
            return false;
        }

        return $this->icon === $rule->icon && $this->otherIcon === $rule->otherIcon;
    }

    public function toArray(): array
    {
        return [
            'type' => self::class,
            'icon' => $this->icon->value,
            'within' => $this->within,
            'otherIcon' => $this->otherIcon->value,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            icon: PlanetXIconEnum::from($data['icon']),
            within: $data['within'],
            otherIcon: PlanetXIconEnum::from($data['otherIcon']),
        );
    }
}
