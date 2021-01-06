<?php

namespace App\Http\Livewire;

use App\Models\Game;
use App\Queue\Events\GameEnded;
use App\Queue\Events\GameRoundAction;
use Livewire\Component;

class WaveLengthComponent extends Component
{
    public Game $game;

    public int $value = 50;

    public ?string $clue = null;

    public function render()
    {
        return view('livewire.WaveLengthComponent');
    }

    /**
     * @return array
     */
    public function getListeners() : array
    {
        $channel = 'Game.' . $this->game->uuid;

        return [
            'echo:' . $channel . ',.' . GameEnded::class       => '$refresh',
            'echo:' . $channel . ',.' . GameRoundAction::class => '$refresh',
        ];
    }

    public function giveClue()
    {
        $this->game->roundAction(['clue' => $this->clue]);
    }

    public function setGuess()
    {
        $this->game->roundAction(['guess' => $this->value]);
    }

    public function nextRound()
    {
        $this->value = 50;
        $this->clue = null;

        $this->game->endRound();
    }
}
