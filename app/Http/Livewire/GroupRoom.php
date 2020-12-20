<?php

namespace App\Http\Livewire;

use App\Models\Game;
use App\Models\Group;
use App\Models\Player;
use App\Queue\Events\PlayerCreated;
use App\Queue\Events\PlayerUpdated;
use Illuminate\Support\Str;
use Livewire\Component;

class GroupRoom extends Component
{
    public Group $group;

    public $color = null;

    public $playerName = null;

    /**
     * @return array
     */
    public function getListeners() : array
    {
        $channel = 'lol';

        return [
            'echo:' . $channel . ',' . PlayerCreated::class => '$refresh',
            'echo:' . $channel . ',' . PlayerUpdated::class => '$refresh',
        ];
    }

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
        event(new PlayerUpdated($this->group->authenticatedPlayer->id));
    }

    public function updatePlayerName()
    {
        $this->group->authenticatedPlayer->name = $this->playerName;
        $this->group->authenticatedPlayer->save();
        event(new PlayerUpdated($this->group->authenticatedPlayer->id));
    }

    public function kickPlayer($playerId)
    {
        Player::find($playerId)->delete();
        event(new PlayerUpdated($playerId));
    }

    public function startGame()
    {
        /** @var Game $game */
        $game = Game::create([
            'uuid'          => Str::uuid(),
            'game_logic_id' => 1,
            'group_id'      => $this->group->id,
            'started_at'    => now(),
        ]);

        $game->startGame();

        $this->redirect('\game\\' . $game->uuid);
    }
}
