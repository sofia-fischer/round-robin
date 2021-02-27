<?php

namespace App\Http\Livewire;

use App\Models\Game;
use App\Models\Player;
use App\Queue\Events\GameEnded;
use App\Queue\Events\GameRoundAction;
use App\Queue\Events\GameStarted;
use App\Queue\Events\PlayerKicked;
use App\Queue\Events\PlayerUpdated;
use Livewire\Component;

class OneNightWerewolfComponent extends Component
{
    public Game $game;

    public bool $showwerewolf = false;

    public bool $showmason = false;

    public bool $showminion = false;

    public bool $showseer = false;

    public bool $showtroublemaker = false;

    public bool $showrobber = false;

    public bool $showvillager = false;

    public bool $showdrunk = false;

    public bool $showtanner = false;

    public bool $showinsomniac = false;

    public function render()
    {
        $roundInfos = $this->game->currentRound->payload ?? [];

        $step = 'start';

        if ($roundInfos) {
            $step = $roundInfos['state'];
        }

        if ($this->game->currentRound->completed_at ?? false) {
            $step = 'end';
        }

        $playerByRoles = [];
        collect($roundInfos['playerRoles'] ?? [])->map(function ($role, $playerId) use (&$playerByRoles) {
            return $playerByRoles[$role] = [...($playerByRoles[$role] ?? []), $playerId];
        });

        return view('livewire.OneNightWerewolfComponent', [
            'step'           => $step,
            'roles'          => collect($roundInfos['playerRoles'] ?? [])->merge($roundInfos['extraRoles'] ?? [])->values()->countBy()->toArray(),
            'playerRole'     => $roundInfos['playerRoles'][$this->game->authenticatedPlayer->id] ?? 'watcher',
            'newPlayerRole'  => $roundInfos['newPlayerRoles'][$this->game->authenticatedPlayer->id] ?? 'watcher',
            'playerByRoles'  => $playerByRoles,
            'playerRoles'    => $roundInfos['playerRoles'] ?? [],
            'newPlayerRoles' => $roundInfos['newPlayerRoles'] ?? [],
            'extraRoles'     => $roundInfos['extraRoles'] ?? [],
            'players'        => $this->game->players->mapWithKeys(function (Player $player) {
                return [$player->id => $player];
            }),
            'votedPlayerId'  => $this->game ? $this->game->authenticatedPlayerMove->payload['vote'] ?? null : null,
        ]);
    }

    public function getListeners() : array
    {
        return [
            'echo:' . 'Game.' . $this->game->uuid . ',.' . GameStarted::class           => '$refresh',
            'echo:' . 'Game.' . $this->game->uuid . ',.' . GameRoundAction::class       => '$refresh',
            'echo:' . 'Game.' . $this->game->uuid . ',.' . GameEnded::class             => '$refresh',
            'echo:' . 'Group.' . $this->game->group->uuid . ',.' . PlayerUpdated::class => '$refresh',
            'echo:' . 'Group.' . $this->game->group->uuid . ',.' . PlayerKicked::class  => 'nextRound',
        ];
    }

    public function startGame()
    {
        $this->game->start();
    }

    public function performAction($action, $contentId)
    {
        $this->game->roundAction(array_merge($this->game->authenticatedPlayerMove->payload ?? [], [$action => $contentId]));
    }

    public function nextRound()
    {
        $this->game->endRound();
    }
}
