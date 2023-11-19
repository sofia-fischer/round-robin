<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Game;
use Livewire\Component;
use App\Queue\Events\PlayerDestroyed;
use App\Queue\Events\PlayerUpdated;
use App\Queue\Events\GameRoundAction;

class   PlayerOverviewComponent extends Component
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
            'echo:' . 'Game.' . $this->game->id . ',.' . GameRoundAction::class  => '$refresh',
            'echo:' . 'Game.' . $this->game->id . ',.' . PlayerUpdated::class   => '$refresh',
            'echo:' . 'Game.' . $this->game->id . ',.' . PlayerDestroyed::class => '$refresh',
        ];
    }
}
