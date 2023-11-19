<?php

namespace App\ValueObjects\WerewolfMoves;

use App\ValueObjects\ColorEnum;
use App\ValueObjects\Enums\WerewolfRoleEnum;
use Illuminate\Contracts\Support\Arrayable;

class SeeMove implements Arrayable
{
    public function __construct(
        public readonly string           $watcherId,
        public readonly string           $seeId,
        public readonly WerewolfRoleEnum $sawRole,
        public readonly string           $sawName,
        public readonly ColorEnum        $sawColor,
    ) {
    }

    public function toArray(): array
    {
        return [
            'move' => self::class,
            'watcherId' => $this->watcherId,
            'seeId' => $this->seeId,
            'sawRole' => $this->sawRole->value,
            'sawName' => $this->sawName,
            'sawColor' => $this->sawColor->value,
        ];
    }

    public static function fromArray(array $array): SeeMove
    {
        return new self(
            watcherId: $array['watcherId'],
            seeId: $array['seeId'],
            sawRole: WerewolfRoleEnum::from($array['sawRole']),
            sawName: $array['sawName'],
            sawColor: ColorEnum::from($array['sawColor']),
        );
    }
}
