<?php

declare(strict_types=1);

namespace App\ValueObjects\PlanetXRules;

use App\ValueObjects\Enums\PlanetXIconEnum;
use App\ValueObjects\PlanetXBoard;

class NotWithinNSectorsRule extends PlanetXRule
{
    public function __construct(
        public PlanetXIconEnum $icon,
        public int             $notWithin,
        public PlanetXIconEnum $otherIcon,
    ) {
    }

    public function isValid(PlanetXBoard $board): bool
    {
        foreach ($board as $index => $sector) {
            if (! $sector->hasIcon($this->icon)) {
                continue;
            }

            for ($countWithin = 1; $countWithin <= $this->notWithin; $countWithin++) {
                $nextIndex = ($index + $countWithin) % 12;
                if ($board->getSector($nextIndex)->hasIcon($this->otherIcon)) {

                    $this->errorMessage = "Sector " . ($index + 1) . " does have " . $this->icon->value
                        . " within " . $this->notWithin . " sectors of " . $this->otherIcon->value;

                    return false;
                }

                $previousIndex = ($index + (12 - $countWithin)) % 12;
                if ($board->getSector($previousIndex)->hasIcon($this->otherIcon)) {

                    $this->errorMessage = "Sector " . ($index + 1) . " does have " . $this->icon->value
                        . " within " . $this->notWithin . " sectors of " . $this->otherIcon->value;

                    return false;
                }
            }
        }

        return true;
    }

    public function toArray(): array
    {
        return [
            'type' => self::class,
            'icon' => $this->icon->value,
            'notWithin' => $this->notWithin,
            'otherIcon' => $this->otherIcon->value,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            icon: PlanetXIconEnum::from($data['icon']),
            notWithin: $data['notWithin'],
            otherIcon: PlanetXIconEnum::from($data['otherIcon']),
        );
    }
}
