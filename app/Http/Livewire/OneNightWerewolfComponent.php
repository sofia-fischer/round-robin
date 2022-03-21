<?php

namespace App\Http\Livewire;

use App\Models\Player;
use Livewire\Component;
use App\Models\WerewolfGame;
use App\Queue\Events\GameEnded;
use App\Queue\Events\GameStarted;
use App\Queue\Events\PlayerUpdated;
use App\Queue\Events\GameRoundAction;
use App\Queue\Events\PlayerDestroyed;

class OneNightWerewolfComponent extends Component
{
    public WerewolfGame $game;

    public function render()
    {
        $roundInfos = $this->game->currentRound->payload ?? [];

        return view('livewire.OneNightWerewolfComponent', [
            'extraRoles'     => $roundInfos['extraRoles'] ?? [],
            'players'        => $this->game->players->mapWithKeys(fn (Player $player) => [$player->id => $player]),
            'votedPlayerId'  => $this->game ? $this->game->authenticatedPlayerMove->payload['vote'] ?? null : null,
        ]);
    }

    public function getListeners(): array
    {
        return [
            'echo:' . 'Game.' . $this->game->uuid . ',.' . GameStarted::class     => '$refresh',
            'echo:' . 'Game.' . $this->game->uuid . ',.' . GameRoundAction::class => '$refresh',
            'echo:' . 'Game.' . $this->game->uuid . ',.' . GameEnded::class       => '$refresh',
            'echo:' . 'Game.' . $this->game->uuid . ',.' . PlayerUpdated::class   => '$refresh',
            'echo:' . 'Game.' . $this->game->uuid . ',.' . PlayerDestroyed::class => '$refresh',
        ];
    }

    public function performAction($action, $contentId)
    {
        $this->game->roundAction(array_merge($this->game->authenticatedPlayerMove->payload ?? [], [$action => $contentId]));
    }
}
