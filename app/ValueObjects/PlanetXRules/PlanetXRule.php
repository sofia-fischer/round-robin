<?php

declare(strict_types=1);

namespace App\ValueObjects\PlanetXRules;

use App\ValueObjects\PlanetXBoard;
use Illuminate\Contracts\Support\Arrayable;

abstract class PlanetXRule implements Arrayable
{
    protected string $errorMessage = '';

    abstract public function isValid(PlanetXBoard $board): bool;

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    abstract public function toArray(): array;

    public function equals(PlanetXRule $rule): bool
    {
        return $this->toArray() === $rule->toArray();
    }

    static public function fromArray(array $data): PlanetXRule
    {
        return match ($data['type']) {
            NotNextToRule::class => NotNextToRule::fromArray($data),
            NextToRule::class => NextToRule::fromArray($data),
            InSectorRule::class => InSectorRule::fromArray($data),
            NotInSectorRule::class => NotInSectorRule::fromArray($data),
            InABandOfNSectorsRule::class => InABandOfNSectorsRule::fromArray($data),
            NotWithinNSectorsRule::class => NotWithinNSectorsRule::fromArray($data),
            WithinNSectorsRule::class => WithinNSectorsRule::fromArray($data),
            CountInSectorsRule::class => CountInSectorsRule::fromArray($data),
            default => throw new \Exception('Unknown rule type: ' . $data['type']),
        };
    }
}
