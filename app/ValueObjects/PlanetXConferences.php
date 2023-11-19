<?php

declare(strict_types=1);

namespace App\ValueObjects;

use App\ValueObjects\PlanetXRules\PlanetXRule;
use Illuminate\Contracts\Support\Arrayable;
use Iterator;

class PlanetXConferences implements Arrayable, Iterator
{
    private int $position = 0;

    public function __construct(
        public ?PlanetXRule $alpha = null,
        public ?PlanetXRule $beta = null,
        public ?PlanetXRule $gamma = null,
        public ?PlanetXRule $delta = null,
        public ?PlanetXRule $epsilon = null,
        public ?PlanetXRule $roh = null,
        public ?PlanetXRule $xConference = null,
    ) {
    }

    /**
     * @return array<string, array>
     */
    public function toArray(): array
    {
        return [
            'a' => $this->alpha?->toArray(),
            'b' => $this->beta?->toArray(),
            'c' => $this->gamma?->toArray(),
            'd' => $this->delta?->toArray(),
            'e' => $this->epsilon?->toArray(),
            'f' => $this->roh?->toArray(),
            'x' => $this->xConference?->toArray(),
        ];
    }

    public static function fromArray(array $data)
    {
        return new self(
            alpha: isset($data['a']) ? PlanetXRule::fromArray($data['a']) : null,
            beta: isset($data['b']) ? PlanetXRule::fromArray($data['b']) : null,
            gamma: isset($data['c']) ? PlanetXRule::fromArray($data['c']) : null,
            delta: isset($data['d']) ? PlanetXRule::fromArray($data['d']) : null,
            epsilon: isset($data['e']) ? PlanetXRule::fromArray($data['e']) : null,
            roh: isset($data['f']) ? PlanetXRule::fromArray($data['f']) : null,
            xConference: isset($data['x']) ? PlanetXRule::fromArray($data['x']) : null,
        );
    }

    public function current(): mixed
    {
        return match ($this->position) {
            0 => $this->alpha,
            1 => $this->beta,
            2 => $this->gamma,
            3 => $this->delta,
            4 => $this->epsilon,
            5 => $this->roh,
            6 => $this->xConference,
        };
    }

    public function next(): void
    {
        $this->position++;
    }

    public function key(): mixed
    {
        return match ($this->position) {
            0 => 'A',
            1 => 'B',
            2 => 'C',
            3 => 'D',
            4 => 'E',
            5 => 'F',
            6 => 'X',
        };
    }

    public function valid(): bool
    {
        return $this->position < 7;
    }

    public function rewind(): void
    {
        $this->position = 0;
    }
}
