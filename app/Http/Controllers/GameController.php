<?php


namespace App\Http\Controllers;


use App\Http\Requests\GameCreateRequest;
use App\Http\Requests\JoinGameRequest;
use App\Models\Game;
use App\Models\JustOneGame;
use App\Models\PlanetXGame;
use App\Models\WaveLengthGame;
use App\Models\WerewolfGame;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GameController
{
    public function join(JoinGameRequest $request)
    {
        /** @var Game $game */
        $game = Game::query()->where('token', $request->input('token'))->firstOrFail();

        return redirect(route("{$game->logic_identifier}.show", ['game' => $game,]));
    }

    public function index()
    {
        return view('GameIndexPage', [
            'waveLengthGames' => WaveLengthGame::query()
                ->withCount(['players', 'rounds'])
                ->whereHas('authenticatedPlayer')
                ->get(),
            'werewolfGames' => WerewolfGame::query()
                ->withCount(['players', 'rounds'])
                ->whereHas('authenticatedPlayer')
                ->get(),
            'justOneGames' => JustOneGame::query()
                ->withCount(['players', 'rounds'])
                ->whereHas('authenticatedPlayer')
                ->get(),
            'planetXGames' => PlanetXGame::query()
                ->withCount(['players', 'rounds'])
                ->whereHas('authenticatedPlayer')
                ->get(),
        ]);
    }

    public function create(GameCreateRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        Game::query()
            ->where('host_user_id', $user->id)
            ->where('logic_identifier', $request->input('logic'))
            ->delete();

        /** @var Game $game */
        $game = Game::create([
            'token' => Str::upper(Str::random(5)),
            'logic_identifier' => $request->input('logic'),
            'host_user_id' => $user->id,
        ]);

        return redirect(route("{$game->logic_identifier}.show", ['game' => $game,]));
    }

    public function destroy(Game $game)
    {
        throw_unless($game->host_user_id === Auth::id(), AuthorizationException::class);

        $game->delete();

        return redirect(route('game.index'));
    }

    public function round(Game $game)
    {
        throw_unless($game->host_user_id === Auth::id(), AuthorizationException::class);

        $game->currentRound->completed_at = now();
        $game->currentRound->save();

        return redirect(route("{$game->logic_identifier}.round", ['game' => $game,]));
    }

    public function settings(Game $game)
    {
        return view('GameSettingsPage', ['game' => $game]);
    }
}
