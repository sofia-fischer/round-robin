<?php

declare(strict_types=1);

namespace App\Exceptions;

class PlanetXBoardGenerationException extends \Exception
{
    private array $board;

    public function __construct(string $message, array $board)
    {
        parent::__construct($message);
        $this->board = $board;
    }

    public function context(): array
    {
        return ['board' => $this->board];
    }
}
