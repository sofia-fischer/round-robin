<?php

declare(strict_types=1);

namespace App\Http\Controllers;


use App\Models\Player;
use App\Queue\Events\PlayerDestroyed;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;

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
