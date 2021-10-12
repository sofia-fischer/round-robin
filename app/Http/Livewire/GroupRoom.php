<?php

namespace App\Http\Livewire;

use App\Models\Game;
use App\Models\Group;
use Livewire\Component;
use Illuminate\Support\Str;
use App\Queue\Events\PlayerKicked;
use App\Queue\Events\PlayerCreated;
use App\Queue\Events\PlayerUpdated;

class GroupRoom extends Component
{
    public Group $group;

    /**
     * @return array
     */
    public function getListeners(): array
    {
        $channel = 'Group.' . $this->group->uuid;

        return [
            'echo:' . $channel . ',.' . PlayerCreated::class => '$refresh',
            'echo:' . $channel . ',.' . PlayerUpdated::class => '$refresh',
            'echo:' . $channel . ',.' . PlayerKicked::class  => '$refresh',
        ];
    }

    public function render()
    {
        if (! $this->group->authenticatedPlayer) {
            return $this->redirect('/welcome');
        }

        return view('livewire.group-room');
    }

    public function startNewGame($logicId)
    {
        $this->group->touch();

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

        $this->redirect('\game\\' . $game->uuid);
    }
}
