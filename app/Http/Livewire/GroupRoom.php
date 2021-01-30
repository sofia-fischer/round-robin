<?php

namespace App\Http\Livewire;

use App\Models\Game;
use App\Models\Group;
use App\Models\User;
use App\Queue\Events\PlayerCreated;
use App\Queue\Events\PlayerUpdated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class GroupRoom extends Component
{
    public Group $group;

    /**
     * @return array
     */
    public function getListeners() : array
    {
        $channel = 'Group.' . $this->group->uuid;

        return [
            'echo:' . $channel . ',.' . PlayerCreated::class => '$refresh',
            'echo:' . $channel . ',.' . PlayerUpdated::class => '$refresh',
        ];
    }

    public function render()
    {
        if (!$this->group->authenticatedPlayer) {
            return $this->redirect('\welcome');
        }

        return view('livewire.group-room');
    }

    public function startNewGame($logicId)
    {
        $this->group->touch();

        // clean up database
        User::whereNull('email')->where('created_at', '<', now()->subWeek())->delete();
        Group::where('updated_at', '<', now()->subWeek())->delete();

        /** @var Game $game */
        $game = Game::create([
            'uuid'          => Str::uuid(),
            'game_logic_id' => $logicId,
            'group_id'      => $this->group->id,
        ]);

        $this->joinGame($game->id);
    }

    public function joinGame($gameId)
    {
        /** @var Game $game */
        $game = Game::findOrFail($gameId);
        $game->join($this->group->authenticatedPlayer);

        $this->redirect('\group\\' . $this->group->uuid . '\game\\' . $game->uuid);
    }
}
