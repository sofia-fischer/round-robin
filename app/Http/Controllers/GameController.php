<?php


namespace App\Http\Controllers;


use App\Models\Game;
use Illuminate\Support\Str;
use App\Models\JustOneGame;
use App\Models\WerewolfGame;
use App\Models\WaveLengthGame;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\JoinGameRequest;
use App\Http\Requests\GameCreateRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class GameController
{
    public function show(Game $game)
    {
        if ($game->logic_identifier === WaveLengthGame::$logic_identifier) {
            return redirect(route('wavelength.join', ['game' => $game->uuid]));
        }

        if ($game->logic_identifier === JustOneGame::$logic_identifier) {
            return redirect(route('justone.join', ['game' => $game->uuid]));
        }

        if ($game->logic_identifier === WerewolfGame::$logic_identifier) {
            return redirect(route('werewolf.join', ['game' => $game->uuid]));
        }

        return view('GamePage', ['game' => $game]);
    }

    public function join(JoinGameRequest $request)
    {
        /** @var Game $game */
        $game = Game::query()->where('token', $request->input('token'))->firstOrFail();

        if ($game->logic_identifier === WaveLengthGame::$logic_identifier) {
            return redirect(route('wavelength.join', ['game' => $game->uuid]));
        }

        if ($game->logic_identifier === JustOneGame::$logic_identifier) {
            return redirect(route('wavelength.join', ['game' => $game->uuid]));
        }

        return redirect(route('game.show', ['game' => $game,]));
    }

    public function index()
    {
        return view('GameIndexPage', [
            'waveLengthGames' => WaveLengthGame::query()
                ->withCount(['players', 'rounds'])
                ->whereHas('authenticatedPlayer')
                ->get(),
            'werewolfGames'   => WerewolfGame::query()
                ->withCount(['players', 'rounds'])
                ->whereHas('authenticatedPlayer')
                ->get(),
            'justOneGames'    => JustOneGame::query()
                ->withCount(['players', 'rounds'])
                ->whereHas('authenticatedPlayer')
                ->get(),
        ]);
    }

    public function create(GameCreateRequest $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (! $user->email) {
            throw new UnauthorizedHttpException();
        }

        /** @var Game $game */
        $game = Game::create([
            'token'            => Str::upper(Str::random(5)),
            'logic_identifier' => $request->input('logic'),
            'host_user_id'     => $user->id,
        ]);

        return redirect(route('game.show', ['game' => $game,]));
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

        if ($game->logic_identifier === WaveLengthGame::$logic_identifier) {
            return redirect(route('wavelength.round', ['game' => $game->uuid]));
        }

        if ($game->logic_identifier === JustOneGame::$logic_identifier) {
            return redirect(route('justone.round', ['game' => $game->uuid]));
        }

        if ($game->logic_identifier === WerewolfGame::$logic_identifier) {
            return redirect(route('werewolf.round', ['game' => $game->uuid]));
        }

        return redirect(route('game.show', ['game' => $game,]));
    }

    public function settings(Game $game)
    {
        return view('GameSettingsPage', ['game' => $game]);
    }
}
