<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use LEVELS\Analytics\Tracking\Queue\Events\CalculationQueued;

/**
 * Class Game
 *
 * @property-read bool $isCompleted
 * @see \App\Models\WaveLengthGame::getIsCompletedAttribute()
 * @property-read bool $isWaitingForClue
 * @see \App\Models\WaveLengthGame::getIsWaitingForClueAttribute()
 * @property-read bool $isWaitingForGuess
 * @see \App\Models\WaveLengthGame::getIsWaitingForGuessAttribute()
 *
 * @package App\Models
 */
class WaveLengthGame extends Game
{
    protected $table = 'games';
    static $logic_identifier = 'wavelength';

    static $title = 'Wavelength';

    static $description = 'The active Player knows where the target on a spectrum between two opposing concepts is,
            but can only give a verbal clue to the other players, who only see the opposing concepts.
            With that clue, the other players have to guess where the target is.';

    public static function query(): Builder
    {
        return parent::query()->where('logic_identifier', self::$logic_identifier);
    }

    protected function getIsCompletedAttribute(): bool
    {
        return (bool) $this->currentRound?->completed_at;
    }

    protected function getIsWaitingForClueAttribute(): bool
    {
        return (! $this->currentRound?->completed_at) && (! $this->currentPayloadAttribute('clue'));
    }

    protected function getIsWaitingForGuessAttribute(): bool
    {
        return (! $this->currentRound?->completed_at) && $this->currentPayloadAttribute('clue');
    }
}
