<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\WerewolfGame;
use App\Queue\Events\GameEnded;
use App\Queue\Events\PlayerUpdated;
use App\Queue\Events\GameRoundAction;
use App\Queue\Events\PlayerDestroyed;

class OneNightWerewolfComponent extends Component
{
    public WerewolfGame $game;

    public function render()
    {
        return view('livewire.OneNightWerewolfComponent');
    }

    public function getListeners(): array
    {
        return [
            'echo:' . 'Game.' . $this->game->uuid . ',.' . GameRoundAction::class => '$refresh',
            'echo:' . 'Game.' . $this->game->uuid . ',.' . GameEnded::class       => '$refresh',
            'echo:' . 'Game.' . $this->game->uuid . ',.' . PlayerUpdated::class   => '$refresh',
            'echo:' . 'Game.' . $this->game->uuid . ',.' . PlayerDestroyed::class => '$refresh',
        ];
    }
}
