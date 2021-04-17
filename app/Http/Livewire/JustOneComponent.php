<?php

namespace App\Http\Livewire;

use App\Models\Game;
use App\Queue\Events\GameRoundAction;
use App\Queue\Events\GameStarted;
use App\Queue\Events\PlayerKicked;
use App\Queue\Events\PlayerUpdated;
use App\Support\GamePolicies\JustOnePolicy;
use Livewire\Component;
use Livewire\Livewire;

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
    public function getListeners() : array
    {
        return [
            'echo:' . 'Game.' . $this->game->uuid . ',.' . GameStarted::class           => '$refresh',
            'echo:' . 'Game.' . $this->game->uuid . ',.' . GameRoundAction::class       => '$refresh',
            'echo:' . 'Group.' . $this->game->group->uuid . ',.' . PlayerUpdated::class => '$refresh',
            'echo:' . 'Group.' . $this->game->group->uuid . ',.' . PlayerKicked::class  => 'nextRound',
        ];
    }

    public function giveClue()
    {
        JustOnePolicy::giveClue($this->game->currentRound, $this->clue);
    }

    public function giveGuess()
    {
        JustOnePolicy::giveGuess($this->game->currentRound, $this->value);
    }

    public function nextRound()
    {
        $this->value = null;
        $this->clue = null;

        JustOnePolicy::nextRound($this->game->currentRound);
    }
}
