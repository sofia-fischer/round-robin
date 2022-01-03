<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\WaveLengthGame;
use App\Queue\Events\GameEnded;
use App\Queue\Events\PlayerUpdated;
use App\Queue\Events\PlayerDestroyed;
use App\Queue\Events\GameRoundAction;

class WaveLengthComponent extends Component
{
    public WaveLengthGame $game;

    public function render()
    {
        return view('livewire.WaveLengthComponent', [
            'moves' => $this->game->currentRound?->moves()
                ->where('player_id', '!=', $this->game->currentRound->active_player_id)
                ->get() ?? [],
        ]);
    }

    /**
     * @return array
     */
    public function getListeners(): array
    {
        return [
            'echo:' . 'Game.' . $this->game->uuid . ',.' . GameRoundAction::class => '$refresh',
            'echo:' . 'Game.' . $this->game->uuid . ',.' . GameEnded::class       => '$refresh',
            'echo:' . 'Game.' . $this->game->uuid . ',.' . PlayerUpdated::class   => '$refresh',
            'echo:' . 'Game.' . $this->game->uuid . ',.' . PlayerDestroyed::class => 'nextRound',
        ];
    }
}
