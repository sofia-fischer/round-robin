<?php

namespace App\Http\Livewire;

use App\Models\Game;
use App\Models\Group;
use App\Models\Player;
use App\Queue\Events\PlayerCreated;
use App\Queue\Events\PlayerUpdated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class GroupRoom extends Component
{
    public Group $group;

    public ?string $color = null;

    public ?string $playerName = null;

    public ?int $gameId = null;

    /**
     * @return array
     */
    public function getListeners() : array
    {
        $channel = 'Group.' . $this->group->uuid;

        return [
            'echo:' . $channel . ',.' . PlayerCreated::class => '$refresh',
            'echo:' . $channel . ',.' . PlayerUpdated::class => '$refresh',
            'refreshPage'                                    => '$refresh',
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

    public function startNewGame($logicId)
    {
        /** @var Game $game */
        $game = Game::create([
            'uuid'          => Str::uuid(),
            'game_logic_id' => $logicId,
            'group_id'      => $this->group->id,
        ]);

        $this->gameId = $game->id;

        $this->emit('refreshPage');
    }

    public function joinGame()
    {
        /** @var Game $game */
        $game = Game::findOrFail($this->gameId);

        if ($game->started_at) {
            $this->redirect('\game\\' . $game->uuid);

            return;
        }

        if ($this->group->host_user_id != Auth::id()) {
            return;
        }

        $game->start();

        $this->redirect('\game\\' . $game->uuid);
    }
}
