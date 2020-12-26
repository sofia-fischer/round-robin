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
    public ?Game  $game = null;

    public ?Group  $group = null;

    public bool $showKickPlayerModal = false;

    public bool $surePlayerKick = false;

    public ?int $kickPlayerId = null;

    public function render()
    {
        return view('livewire.PlayerOverviewComponent');
    }

    public function mount()
    {
        if (!$this->group) {
            $this->group = $this->game->group;
        }
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
}
