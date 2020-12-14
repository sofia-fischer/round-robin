<?php

namespace App\Http\Livewire;

use App\Models\Game;
use App\Models\Group;
use App\Models\Player;
use Livewire\Component;

class GroupRoom extends Component
{
    public Group $group;

    public $color = null;

    public $playerName = null;

    public function render()
    {
        if (!$this->group->authenticatedPlayer) {
            return $this->redirect('\welcome');
        }

        $this->playerName = $this->group->authenticatedPlayer->name;
        $this->color = $this->group->authenticatedPlayer->color;

        return view('livewire.group-room');
    }

    public function updateColor()
    {
        $this->group->authenticatedPlayer->color = $this->color;
        $this->group->authenticatedPlayer->save();
    }

    public function updatePlayerName()
    {
        $this->group->authenticatedPlayer->name = $this->playerName;
        $this->group->authenticatedPlayer->save();
    }

    public function kickPlayer($playerId)
    {
        Player::find($playerId)->delete();
    }

    public function startGame()
    {
        Game::create([
            'game_logic_id' => 1,
            'group_id'      => $this->group->id,
            'started_at'    => now(),
        ]);
    }
}
