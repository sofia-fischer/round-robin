<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use Livewire\Livewire;
use Livewire\Component;
use App\Models\JustOneGame;
use App\Queue\Events\PlayerUpdated;
use App\Queue\Events\PlayerDestroyed;
use App\Queue\Events\GameRoundAction;

Livewire::component('just-one-component', JustOneComponent::class);

class JustOneComponent extends Component
{
    public JustOneGame $game;

    public function render()
    {
        return view('livewire.JustOneComponent');
    }

    /**
     * @return array
     */
    public function getListeners(): array
    {
        return [
            'echo:' . 'Game.' . $this->game->id . ',.' . GameRoundAction::class => '$refresh',
            'echo:' . 'Game.' . $this->game->id . ',.' . PlayerUpdated::class   => '$refresh',
            'echo:' . 'Game.' . $this->game->id . ',.' . PlayerDestroyed::class => '$refresh',
        ];
    }
}
