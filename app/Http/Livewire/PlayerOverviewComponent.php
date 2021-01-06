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

    public bool $showKickPlayerModal = false;

    public bool $surePlayerKick = false;

    public ?int $kickPlayerId = null;

    public ?string $color = null;

    public ?string $playerName = null;

    public function render()
    {
        return view('livewire.PlayerOverviewComponent');
    }

    public function mount()
    {
        if (!$this->group) {
            $this->group = $this->game->group;
        }

        $this->playerName = $this->group->authenticatedPlayer->name;
        $this->color = $this->group->authenticatedPlayer->color;
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

    public function kickPlayer()
    {
        if (!$this->surePlayerKick || !$this->kickPlayerId) {
            return;
        }

        Player::find($this->kickPlayerId)->delete();
        event(new PlayerUpdated($this->kickPlayerId));
        $this->surePlayerKick = false;
        $this->kickPlayerId = null;
        $this->showKickPlayerModal = false;
    }

    public function saveSettings()
    {
        $this->group->authenticatedPlayer->name = $this->playerName;
        $this->group->authenticatedPlayer->color = $this->color;
        $this->group->authenticatedPlayer->save();
        $this->showKickPlayerModal = false;
        event(new PlayerUpdated($this->group->authenticatedPlayer->id));
    }
}
