<?php

namespace App\Http\Livewire;

use App\Models\Game;
use Livewire\Component;
use App\Queue\Events\PlayerDestroyed;
use App\Queue\Events\PlayerUpdated;
use App\Queue\Events\GameRoundAction;

class PlayerOverviewComponent extends Component
{
    public ?Game $game = null;

    public function render()
    {
        return view('livewire.PlayerOverviewComponent');
    }

    /**
     * @return array
     */
    public function getListeners(): array
    {
        return [
            'echo:' . 'Game.' . $this->game->uuid . ',.' . GameRoundAction::class  => '$refresh',
            'echo:' . 'Game.' . $this->game->uuid . ',.' . PlayerUpdated::class   => '$refresh',
            'echo:' . 'Game.' . $this->game->uuid . ',.' . PlayerDestroyed::class => '$refresh',
        ];
    }
}
