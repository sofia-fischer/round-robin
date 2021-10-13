<?php

namespace App\Http\Livewire;

use App\Models\Game;
use Livewire\Livewire;
use Livewire\Component;
use App\Queue\Events\GameStarted;
use App\Queue\Events\PlayerUpdated;
use App\Queue\Events\PlayerDestroyed;
use App\Queue\Events\GameRoundAction;
use App\Support\GameLogics\JustOneLogic;

Livewire::component('just-one-component', JustOneComponent::class);

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

        if ($this->game->authenticatedPlayerMove) {
            $this->clue = $this->game->authenticatedPlayerMove->payload['clue'] ?? null;
            $this->value = $this->game->authenticatedPlayerMove->payload['guess'] ?? null;
        }

        return view('livewire.JustOneComponent', [
            'step' => $step,
        ]);
    }

    /**
     * @return array
     */
    public function getListeners(): array
    {
        return [
            'echo:' . 'Game.' . $this->game->uuid . ',.' . GameStarted::class     => '$refresh',
            'echo:' . 'Game.' . $this->game->uuid . ',.' . GameRoundAction::class => '$refresh',
            'echo:' . 'Game.' . $this->game->uuid . ',.' . PlayerUpdated::class   => '$refresh',
            'echo:' . 'Game.' . $this->game->uuid . ',.' . PlayerDestroyed::class => 'nextRound',
        ];
    }

    public function giveClue()
    {
        JustOneLogic::giveClue($this->game->currentRound, $this->clue);
    }

    public function giveGuess()
    {
        JustOneLogic::giveGuess($this->game->currentRound, $this->value);
    }

    public function nextRound()
    {
        $this->value = null;
        $this->clue = null;

        JustOneLogic::nextRound($this->game->currentRound);
    }
}
