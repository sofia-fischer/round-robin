<?php

declare(strict_types=1);

namespace App\ValueObjects\PlanetXRules;

use App\ValueObjects\Enums\PlanetXIconEnum;

class CountInManySectorsRule extends CountInFewSectorsRule
{
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
