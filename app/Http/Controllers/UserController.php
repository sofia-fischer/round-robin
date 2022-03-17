<?php


namespace App\Http\Controllers;


use App\Models\Game;
use App\Models\User;
use App\Models\Player;
use App\Queue\Events\PlayerUpdated;
use Illuminate\Support\Facades\Auth;
use App\Queue\Events\PlayerDestroyed;
use App\Http\Requests\UserUpdateRequest;
use Illuminate\Auth\Access\AuthorizationException;

class UserController
{
    public function destroy(User $user)
    {
        throw_unless($user->id === Auth::id(), AuthorizationException::class);

        $players = $user->players;

        Game::query()
            ->where('host_user_id', $user->id)
            ->with('players')
            ->get()
            ->map(function (Game $game) use ($user) {
                $newHost = $game->players->firstWhere('user_id', '!=', $user->id);

                if (! $newHost) {
                    $game->delete();
                }

                $game->host_user_id = $newHost->user_id;
                $game->save();
            });

        $user->delete();
        $user->players()->delete();
        $players->each(fn (Player $player) => event(new PlayerDestroyed($player->id)));

        return redirect()->route('game.settings', ['game' => $user->game]);
    }

    public function update(UserUpdateRequest $request, User $user)
    {
        throw_unless($user->id === Auth::id(), AuthorizationException::class);

        $user->update($request->data());
        $user->players->each(fn (Player $player) => event(new PlayerUpdated($player->id)));

        $game = $request->get('game_id') ? Game::find($request->get('game_id')) : null;

        return redirect()->route('game.settings', ['game' => $game]);
    }
}
