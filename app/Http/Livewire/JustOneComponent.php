<?php

namespace App\Http\Livewire;

use App\Models\Game;
use App\Queue\Events\GameEnded;
use App\Queue\Events\GameRoundAction;
use App\Queue\Events\GameStarted;
use App\Queue\Events\PlayerKicked;
use App\Queue\Events\PlayerUpdated;
use Livewire\Component;

class JustOneComponent extends Component
{
    public Game $game;

    public ?string $value = null;

    public ?string $clue = null;

    public function render()
    {
        $step = 'start';

        if ($this->game->currentRound->payload['clues_calculated'] ?? false) {
            $step = 'clue-given';
        }

        if ($this->game->currentRound->completed_at) {
            $step = 'completed';
        }

        return view('livewire.JustOneComponent', [
            'step' => $step,
        ]);
    }

    /**
     * @return array
     */
    public function getListeners() : array
    {
        return [
            'echo:' . 'Game.' . $this->game->uuid . ',.' . GameStarted::class           => '$refresh',
            'echo:' . 'Game.' . $this->game->uuid . ',.' . GameRoundAction::class       => '$refresh',
            'echo:' . 'Game.' . $this->game->uuid . ',.' . GameEnded::class             => '$refresh',
            'echo:' . 'Group.' . $this->game->group->uuid . ',.' . PlayerUpdated::class => '$refresh',
            'echo:' . 'Group.' . $this->game->group->uuid . ',.' . PlayerKicked::class  => 'nextRound',
        ];
    }

    public function giveClue()
    {
        $this->game->roundAction(['clue' => $this->clue]);
    }

    public function giveGuess()
    {
        $this->game->roundAction(['guess' => $this->value]);
    }

    public function nextRound()
    {
        $this->value = null;
        $this->clue = null;

        $this->game->endRound();
    }
}
