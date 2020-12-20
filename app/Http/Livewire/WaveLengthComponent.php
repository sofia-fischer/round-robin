<?php

namespace App\Http\Livewire;

use App\Models\Game;
use Livewire\Component;

class WaveLengthComponent extends Component
{
    public int $gameId;

    public int $value = 50;

    public ?string $clue = null;

    public function render()
    {
        /** @var Game $game */
        $game = Game::find($this->gameId);

        return view('livewire.WaveLengthComponent', [
            'round' => $game->currentRound,
        ]);
    }

    public function giveClue()
    {
        /** @var Game $game */
        $game = Game::find($this->gameId);
        $game->roundAction(['clue' => $this->clue]);
    }

    public function setGuess()
    {
        /** @var Game $game */
        $game = Game::find($this->gameId);
        $game->roundAction(['guess' => $this->value]);
    }

    public function nextRound()
    {
        $this->value = 50;
        $this->clue = null;

        /** @var Game $game */
        $game = Game::find($this->gameId);
        $game->nextRound();
    }
}
