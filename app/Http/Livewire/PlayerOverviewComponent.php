<?php

namespace App\Http\Livewire;

use App\Models\Game;
use App\Models\Group;
use App\Models\Player;
use App\Queue\Events\GameRoundAction;
use App\Queue\Events\PlayerUpdated;
use Livewire\Component;

class PlayerOverviewComponent extends Component
{
    public ?Game $game = null;

    public ?Group $group = null;

    public function render()
    {
        return view('livewire.PlayerOverviewComponent');
    }

    /**
     * @return array
     */
    public function getListeners() : array
    {
        if (!$this->game) {
            return [
                'echo:' . 'Group.' . $this->group->uuid . ',.' . PlayerUpdated::class => '$refresh',
            ];
        }

        return [
            'echo:' . 'Game.' . $this->game->uuid . ',.' . GameRoundAction::class => '$refresh',
            'echo:' . 'Group.' . $this->group->uuid . ',.' . PlayerUpdated::class => '$refresh',
        ];
    }
}
