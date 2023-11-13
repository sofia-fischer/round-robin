<?php

declare(strict_types=1);

namespace App\ValueObjects\PlanetXRules;

use App\ValueObjects\Enums\PlanetXIconEnum;
use App\ValueObjects\PlanetXBoard;

class InABandOfNSectorsRule extends PlanetXRule
{
    public function __construct(
        public PlanetXIconEnum $icon,
        public int             $within,
    ) {
    }

    public function isValid(PlanetXBoard $board): bool
    {
        // the board is valid if all icons are within n consecutive sectors.
        // therefor the board is valid, if there exists a band of 12-n sectors, which do not contain the icon.
        $numberOfSectorsWithoutTheIcon = 12 - $this->within;

        foreach ($board as $index => $sector) {
            if ($sector->hasIcon($this->icon)) {
                continue;
            }

            for ($countWithoutIcon = 1; $countWithoutIcon < $numberOfSectorsWithoutTheIcon; $countWithoutIcon++) {
                if ($board->getSector(($index + $countWithoutIcon) % 12)->hasIcon($this->icon)) {
                    continue 2;
                }
            }

            // if we reach this point, we have found a band of 12-n sectors, which do not contain the icon.
            return true;
        }

        $this->errorMessage = "There is no band of {$this->within} sectors, which do contain all {$this->icon->value}";

        return false;
    }

    public function toArray(): array
    {
        return [
            'type' => self::class,
            'icon' => $this->icon->value,
            'within' => $this->within,
        ];
    }

    public function equals(PlanetXRule $rule): bool
    {
        if (! $rule instanceof self) {
            return false;
        }

        return $this->icon === $rule->icon;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            icon: PlanetXIconEnum::from($data['icon']),
            within: $data['within'],
        );
    }
}
