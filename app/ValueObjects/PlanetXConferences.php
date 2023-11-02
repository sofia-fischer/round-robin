<?php

namespace App\ValueObjects;

use App\ValueObjects\PlanetXRules\PlanetXRule;
use Illuminate\Contracts\Support\Arrayable;

class PlanetXConferences implements Arrayable
{

    public function __construct(
        public PlanetXRule $alpha,
        public PlanetXRule $beta,
        public PlanetXRule $gamma,
        public PlanetXRule $delta,
        public PlanetXRule $epsilon,
        public PlanetXRule $roh,
        public PlanetXRule $xConference,
    ) {
    }

    /**
     * @return array<string, array>
     */
    public function toArray(): array
    {
        return [
            'a' => $this->alpha->toArray(),
            'b' => $this->beta->toArray(),
            'c' => $this->gamma->toArray(),
            'd' => $this->delta->toArray(),
            'e' => $this->epsilon->toArray(),
            'f' => $this->roh->toArray(),
            'x' => $this->xConference->toArray(),
        ];
    }

    public static function fromArray(array $data)
    {
        return new self(
            alpha: PlanetXRule::fromArray($data['a']),
            beta: PlanetXRule::fromArray($data['b']),
            gamma: PlanetXRule::fromArray($data['c']),
            delta: PlanetXRule::fromArray($data['d']),
            epsilon: PlanetXRule::fromArray($data['e']),
            roh: PlanetXRule::fromArray($data['f']),
            xConference: PlanetXRule::fromArray($data['x']),
        );
    }
}
