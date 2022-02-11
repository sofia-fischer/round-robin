<?php

namespace App\Http\Livewire;

use Livewire\Livewire;
use Livewire\Component;
use App\Models\JustOneGame;
use App\Queue\Events\GameStarted;
use App\Queue\Events\PlayerUpdated;
use App\Queue\Events\PlayerDestroyed;
use App\Queue\Events\GameRoundAction;

Livewire::component('just-one-component', JustOneComponent::class);

class JustOneComponent extends Component
{
    public JustOneGame $game;

    public function render()
    {
//        dd($this->game->word);
        return view('livewire.JustOneComponent');
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
}
