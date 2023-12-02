<?php

declare(strict_types=1);

namespace App\ValueObjects\PlanetXRules;

use App\ValueObjects\Enums\PlanetXIconEnum;
use App\ValueObjects\PlanetXBoard;

class CountInSectorsRule extends PlanetXRule
{
    public function __construct(
        public readonly PlanetXIconEnum $icon,
        public readonly int             $from,
        public readonly int             $to,
        public readonly int             $count,
    ) {
    }

    public function isValid(PlanetXBoard $board): bool
    {
        $absolutTo = $this->to < $this->from ? $this->to + 12 : $this->to;

        $counter = 0;
        foreach (array_map(fn ($sector) => $sector % 12, range($this->from, $absolutTo)) as $index) {
            if ($board->getSector($index)->hasIcon($this->icon)) {
                $counter++;
            }
        }

        if ($counter === $this->count) {
            return true;
        }

        $this->errorMessage = "Between Sector " . $this->from + 1 . " and Sector " . $this->to + 1
            . " there are not " . $this->count . " " . $this->icon->value;

        return false;
    }

    public function toArray(): array
    {
        return [
            'type' => self::class,
            'icon' => $this->icon->value,
            'from' => $this->from,
            'to' => $this->to,
            'count' => $this->count,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            icon: PlanetXIconEnum::from($data['icon']),
            from: $data['from'],
            to: $data['to'],
            count: $data['count'],
        );
    }
}
