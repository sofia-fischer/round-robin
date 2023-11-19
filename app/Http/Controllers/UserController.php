<?php

declare(strict_types=1);

namespace App\Http\Controllers;


use App\Http\Requests\UserUpdateRequest;
use App\Models\Game;
use App\Models\Player;
use App\Models\User;
use App\Queue\Events\PlayerDestroyed;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController
{
    public function view()
    {
        return view('ProfilePage');
    }

    public function destroy()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

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

        $players = Player::query()
            ->where('user_id', $user->id)
            ->get();

        $players->delete();
        $players->each(fn (Player $player) => event(new PlayerDestroyed($player->id)));
        $user->tokens->each->delete();
        $user->delete();

        return redirect()->route('login');
    }

    public function update(UserUpdateRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();

        if ($request->get('name')) {
            $user->name = $request->get('name');
            $user->save();
        }

        if ($request->get('password')) {
            $user->password = Hash::make($request->get('password'));
            $user->save();
        }

        $user->save();

        return redirect()->route('game.index');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
