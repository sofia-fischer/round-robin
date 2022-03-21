<?php


namespace App\Http\Controllers;


use App\Models\Game;
use App\Models\Player;
use App\Queue\Events\PlayerUpdated;
use Illuminate\Support\Facades\Auth;
use App\Queue\Events\PlayerDestroyed;
use App\Http\Requests\UserUpdateRequest;
use Illuminate\Auth\Access\AuthorizationException;

class PlayerController
{
    public function destroy(Player $player)
    {
        throw_unless($player->game->host_user_id === Auth::id() || $player->user_id === Auth::id(), AuthorizationException::class);

        $player->delete();
        event(new PlayerDestroyed($player->id));

        return redirect()->route('game.settings', ['game' => $player->game]);
    }
}
