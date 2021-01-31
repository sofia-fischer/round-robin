<?php

namespace App\Http\Livewire;

use App\Models\Group;
use App\Models\Player;
use App\Queue\Events\PlayerKicked;
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
        /** @var Group $group */
        $group = Group::where('uuid', $this->groupUuid)->firstOrFail();
        if ($group->host_user_id == $this->kickPlayerId) {
            $newHost = $group->players()->where('id', '!=', $this->kickPlayerId)->first();
            if (!$newHost) {
                $group->delete();

                return $this->redirect('/welcome');
            }

            $group->host_user_id = $newHost;
        }

        Player::find($this->kickPlayerId)->delete();

        event(new PlayerKicked($this->kickPlayerId));

        if (!$group->authenticatedPlayer) {
            return $this->redirect('/welcome');
        }

        $this->kickPlayerId = null;
        $this->emit('game-settings-close');
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
