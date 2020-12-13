<?php

namespace App\Http\Livewire;

use App\Models\Game;
use App\Models\GameLogic;
use App\Models\Group;
use App\Models\Player;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class StartGameComponent extends Component
{
    public function render()
    {
        return view('livewire.start-game-component', [
            'games' => GameLogic::all(),
        ]);
    }

    public function startGame($gameId)
    {
        $logic = GameLogic::findOrFail($gameId);

        // clear all old groups of the host
        Group::where('host_user_id', Auth::id())->delete();

        // create a new Group
        $group = Group::create([
            'uuid'         => Str::uuid(),
            'host_user_id' => Auth::id(),
        ]);

        // create Game
        $player = Player::create([
            'uuid'       => Str::uuid(),
            'user_id'    => Auth::id(),
            'name'       => Auth::user()->name,
            'counter'    => 0,
            'group_id'=> $group->id,
        ]);

        $this->redirect('/group/' . $group->uuid);
    }
}
