<?php

namespace App\Http\Livewire;

use App\Models\Group;
use App\Models\Player;
use App\Queue\Events\PlayerCreated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class TopBarComponent extends Component
{
    public function render()
    {
        return view('livewire.TopBarComponent');
    }

    public function newGroup()
    {
        // create a new Group
        $group = Group::create([
            'uuid'         => Str::uuid(),
            'host_user_id' => Auth::id(),
        ]);

        // create Game
        $player = Player::create([
            'uuid'     => Str::uuid(),
            'user_id'  => Auth::id(),
            'name'     => Auth::user()->name,
            'group_id' => $group->id,
        ]);
        event(new PlayerCreated($player->id));

        $this->redirect('/group/' . $group->uuid);
    }
}