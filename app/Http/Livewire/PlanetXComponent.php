<?php

namespace App\Http\Livewire;

use App\Models\PlanetXGame;
use App\Queue\Events\GameRoundAction;
use App\ValueObjects\PlanetXBoardForm;
use Livewire\Component;
use Livewire\Livewire;

Livewire::component('planet-x-component', PlanetXComponent::class);

class PlanetXComponent extends Component
{
    public PlanetXGame $game;

    public PlanetXBoardForm $form;

    public function mount(PlanetXGame $game)
    {
        $this->form->setBoard($game->getAuthenticatedPlayerBoard());
    }

    public function render()
    {
        return view('livewire.PlanetXComponent');
    }

    public function updated($name, $value)
    {
        $this->game->setAuthenticatedPlayerBoard($this->form->getBoard());
    }

    public function getListeners(): array
    {
        return [
            'echo:' . 'Game.' . $this->game->uuid . ',.' . GameRoundAction::class => '$refresh',
        ];
    }
}
