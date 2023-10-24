<?php

namespace App\Http\Livewire;

use App\Models\PlanetXGame;
use App\Queue\Events\GameRoundAction;
use App\ValueObjects\PlanetXRules\PlanetXBoard;
use Livewire\Component;
use Livewire\Livewire;

Livewire::component('just-one-component', PlanetXComponent::class);

class PlanetXComponent extends Component
{
    public PlanetXGame $game;

    public PlanetXBoard $board;

    public function mount()
    {
        $this->board = $this->game->getAuthenticatedPlayerBoard();
    }

    public function render()
    {
        return view('livewire.PlanetXComponent');
    }

    public function hint(int $section, string $icon)
    {
        $this->board->hint($section, $icon);
    }

    /**
     * @return array
     */
    public function getListeners(): array
    {
        return [
            'echo:' . 'Game.' . $this->game->uuid . ',.' . GameRoundAction::class => '$refresh',
        ];
    }
}
