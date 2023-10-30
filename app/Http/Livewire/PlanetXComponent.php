<?php

namespace App\Http\Livewire;

use App\Models\PlanetXGame;
use App\Queue\Events\GameRoundAction;
use App\Services\PlanetXBoardGenerationService;
use App\ValueObjects\Enums\PlanetXIconEnum;
use App\ValueObjects\PlanetXBoard;
use Livewire\Component;
use Livewire\Livewire;

Livewire::component('just-one-component', PlanetXComponent::class);

class PlanetXComponent extends Component
{
    public PlanetXGame $game;

    public PlanetXBoard $board;

    public function mount()
    {
        $service = new PlanetXBoardGenerationService();
        $this->board = $service->generateBoard();
    }

    public function render()
    {
        return view('livewire.PlanetXComponent');
    }

    public function hint(int $section, string $icon)
    {
        $this->board->hint($section, PlanetXIconEnum::from($icon));
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
