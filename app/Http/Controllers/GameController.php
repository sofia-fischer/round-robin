<?php


namespace App\Http\Controllers;


use App\Models\Game;
use Illuminate\Support\Str;
use App\Models\WaveLengthGame;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\GameCreateRequest;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class GameController
{
    public function show(Game $game)
    {
        $game->join();

        switch ($game->logic_identifier) {
            case WaveLengthGame::$logic_identifier:
                $game = WaveLengthGame::find($game->id);
                break;
        }

        return view('GamePage', [
            'game' => $game,
        ]);
    }

    public function index()
    {
        return view('GameIndexPage', [
            'waveLengthGames' => WaveLengthGame::query()
                ->withCount(['players', 'rounds'])
                ->whereHas('authenticatedPlayer')
                ->get(),
            'werewolfGames'   => WaveLengthGame::query()
                ->withCount(['players', 'rounds'])
                ->whereHas('authenticatedPlayer')
                ->get(),
            'justOneGames'    => WaveLengthGame::query()
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
        if (! $game->host_user_id !== Auth::id()) {
            throw new UnauthorizedHttpException();
        }

        $game->delete();

        return redirect(route('game.index'));
    }
}
