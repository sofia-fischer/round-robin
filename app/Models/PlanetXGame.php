<?php

namespace App\Models;

use App\Queue\Events\GameRoundAction;
use App\ValueObjects\PlanetXBoard;
use Illuminate\Database\Eloquent\Builder;

class PlanetXGame extends Game
{
    protected $table = 'games';
    static $logic_identifier = 'planet_x';

    static $title = 'Planet X';

    static $description = 'The sky is separated in 12 Sections.
        In each Section is exactly one object out of Planet X, Gas Cloud, Dwarf Planet, Comet, Asteroid or Empty Space.
        The Players take turns and gather Information or publishing Theories on the different Sections.
        The Game ends when one Person locates Planet X and the surrounding Sections correctly.
        In the end Players are rewarded by correctly published Sections. ';

    public PlanetXBoard $board;

    public static function query(): Builder
    {
        return parent::query()->where('logic_identifier', self::$logic_identifier);
    }

    public function startRound()
    {
        event(new GameRoundAction($this));
    }

    public function getAuthenticatedPlayerBoard(): PlanetXBoard
    {
        return PlanetXBoard::fromArray($this->authenticatedPlayer->payload['board'] ?? []);
    }

    public function storeAuthenticatedPlayerBoard(PlanetXBoard $board): void
    {
        $this->authenticatedPlayer->payload['board'] = $board->toArray();
        $this->authenticatedPlayer->save();
    }
}
