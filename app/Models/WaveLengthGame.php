<?php

namespace App\Models;

use App\Models\Builders\B2BUserBuilder;
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

    protected function getIsCompletedAttribute()
    {
        return ! ! $this->currentRound?->completed_at;
    }

    protected function getIsWaitingForClueAttribute()
    {
        return ! $this->currentRound?->completed_at && ! $this->currentPayloadAttribute('clue');
    }

    protected function getIsWaitingForGuessAttribute()
    {
        return ! $this->currentRound?->completed_at && $this->currentPayloadAttribute('clue');
    }

    public $antonyms = [
        ['alive' => 'dead'],
        ['backward' => 'forward'],
        ['beautiful' => 'ugly'],
        ['big' => 'small'],
        ['blunt' => 'sharp'],
        ['boring' => 'interesting'],
        ['bright' => 'dark'],
        ['broad' => 'narrow'],
        ['clean' => 'dirty'],
        ['intelligent' => 'stupid'],
        ['closed' => 'open'],
        ['cool' => 'warm'],
        ['cruel' => 'kind'],
        ['dangerous' => 'safe'],
        ['dark' => 'light'],
        ['deep' => 'shallow'],
        ['difficult' => 'easy'],
        ['dry' => 'wet'],
        ['early' => 'late'],
        ['fake' => 'real'],
        ['fast' => 'slow'],
        ['flexible' => 'inflexible'],
        ['gentle' => 'fierce'],
        ['good' => 'bad'],
        ['happy' => 'sad'],
        ['hard' => 'soft'],
        ['heavy' => 'light'],
        ['high' => 'low'],
        ['hot' => 'cold'],
        ['ill' => 'well'],
        ['innocent' => 'guilty'],
        ['long' => 'short'],
        ['loose' => 'tight'],
        ['loud' => 'soft'],
        ['low' => 'high'],
        ['modern' => 'ancient'],
        ['noisy' => 'quiet'],
        ['normal' => 'strange'],
        ['useful invention' => 'useless invention'],
        ['old' => 'new'],
        ['outgoing' => 'shy'],
        ['poor' => 'rich'],
        ['moral' => 'cruel'],
        ['rough' => 'smooth'],
        ['short' => 'tall'],
        ['sour' => 'sweet'],
        ['strong' => 'weak'],
        ['terrible' => 'wonderful'],
        ['far' => 'near'],
        ['cheap' => 'expensive'],
        ['low quality' => 'high quality'],
        ['normal greeting' => 'weird greeting'],
        ['bad advice' => 'good advice'],
        ['possible during Corona' => 'not possible during Corona'],
        ['Log Level: Debug' => 'Log Level: Critical'],
        ['😇' => '😏'],
    ];
}
