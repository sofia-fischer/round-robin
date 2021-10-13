<?php


namespace App\Http\Controllers;


use App\Models\Game;
use App\Models\Player;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class PlayerController
{
    public function create(Game $game)
    {
        /** @var Player $player */
        $game->join();

        return redirect()->route('game.show', ['game' => $game]);
    }

    public function destroy(Player $player)
    {
        if ($player->game->host_user_id === Auth::id()) {
            $player->game->destroyPlayer($player);

            return redirect()->route('game.show', ['game' => $player->game]);
        }

        if ($player->id === Auth::id()) {
            $player->game->destroyPlayer($player);

            return redirect()->route('game.index');
        }

        throw new UnauthorizedHttpException();
    }
}
