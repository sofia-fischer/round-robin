<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use LEVELS\Analytics\Tracking\Queue\Events\CalculationQueued;

/**
 * Class Game
 *
 * @property-read string $word
 * @see \App\Models\JustOneGame::getWordAttribute()
 * @property-read string $guess
 * @see \App\Models\JustOneGame::getGuessAttribute()
 * @property-read bool $isCompleted
 * @see \App\Models\JustOneGame::getIsCompletedAttribute()
 * @property-read bool $isWaitingForClue
 * @see \App\Models\JustOneGame::getIsWaitingForClueAttribute()
 * @property-read bool $isWaitingForGuess
 * @see \App\Models\JustOneGame::getIsWaitingForGuessAttribute()
 *
 * @package App\Models
 */
class JustOneGame extends Game
{
    protected $table = 'games';
    static $logic_identifier = 'justone';

    static $title = 'Just One';

    static $description = 'All players but the active player see the same word.
            Without communicating with each other each player but the active player gives a one worded clue.
            After all players made their clue, all clues which are a substring of the original word or duplicated words are hidden.
            The active player then has to guess the word using the visible clues.';

    public static function query(): Builder
    {
        return parent::query()->where('logic_identifier', self::$logic_identifier);
    }

    protected function getWordAttribute(): string
    {
        return $this->currentRound->payloadAttribute('word');
    }

    protected function getGuessAttribute(): string
    {
        /** @var Move $activePlayerMove */
        $activePlayerMove = $this->currentRound->moves->firstWhere('player_id', $this->currentRound->active_player_id);

        return $activePlayerMove?->getPayloadWithKey('guess');
    }

    protected function getIsCompletedAttribute(): bool
    {
        return  (bool) $this->currentRound->completed_at;
    }

    protected function getIsWaitingForClueAttribute(): bool
    {
        return ! $this->currentPayloadAttribute('clues_calculated');
    }

    protected function getIsWaitingForGuessAttribute(): bool
    {
        return ! $this->currentRound->completed_at && $this->currentPayloadAttribute('clues_calculated');
    }
}
