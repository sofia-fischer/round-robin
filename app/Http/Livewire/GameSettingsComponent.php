<?php

namespace App\Http\Livewire;

use App\Models\Group;
use App\Models\Player;
use App\Queue\Events\PlayerUpdated;
use Livewire\Component;

class GameSettingsComponent extends Component
{
    public ?string $groupUuid = null;

    public ?int $kickPlayerId = null;

    public ?string $color = null;

    public ?string $playerName = null;

    public bool $isAdmin = false;

    public function mount()
    {
        $group = Group::where('uuid', $this->groupUuid)->firstOrFail();
        $this->playerName = $group->authenticatedPlayer->name;
        $this->color = $group->authenticatedPlayer->color;
        $this->isAdmin = $group->host_user_id == ($group->authenticatedPlayer->user_id ?? false);
    }

    public function render()
    {
        return view('livewire.GameSettingsComponent', [
            'group' => $group = Group::where('uuid', $this->groupUuid)->firstOrFail(),
        ]);
    }

    public function kickPlayer()
    {
        Player::find($this->kickPlayerId)->delete();
        event(new PlayerUpdated($this->kickPlayerId));
        $this->kickPlayerId = null;
    }

    public function saveSettings()
    {
        $group = Group::where('uuid', $this->groupUuid)->firstOrFail();

        $group->authenticatedPlayer->name = $this->playerName;
        $group->authenticatedPlayer->color = $this->color;
        $group->authenticatedPlayer->save();
        event(new PlayerUpdated($group->authenticatedPlayer->id));
        $this->emit('game-settings-close');
    }

}
