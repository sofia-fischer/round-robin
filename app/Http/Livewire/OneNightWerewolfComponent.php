<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\ValueObjects\WerewolfBoard;
use Livewire\Component;
use App\Models\WerewolfGame;
use App\Queue\Events\GameEnded;
use App\Queue\Events\PlayerUpdated;
use App\Queue\Events\GameRoundAction;
use App\Queue\Events\PlayerDestroyed;

class OneNightWerewolfComponent extends Component
{
    public WerewolfGame $game;

    public WerewolfBoard $board;

    public function mount(WerewolfGame $game)
    {
        $this->game = $game;
        $this->board = $game->getCurrentWerewolfBoard();
    }

    public function render()
    {
        return view('livewire.OneNightWerewolfComponent');
    }

    public function getListeners(): array
    {
        return [
            'echo:' . 'Game.' . $this->game->id . ',.' . GameRoundAction::class => '$refresh',
            'echo:' . 'Game.' . $this->game->id . ',.' . GameEnded::class       => '$refresh',
            'echo:' . 'Game.' . $this->game->id . ',.' . PlayerUpdated::class   => '$refresh',
            'echo:' . 'Game.' . $this->game->id . ',.' . PlayerDestroyed::class => '$refresh',
        ];
    }
}
