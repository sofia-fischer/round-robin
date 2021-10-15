<?php


namespace App\Http\Controllers;


use App\Models\Game;
use App\Models\GameLogic;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class GameController
{
    public function show(Game $game)
    {
        if (! $game->authenticatedPlayer) {
            $game->authenticatedPlayer()->create();
        }

        return view('GamePage', [
            'game' => $game,
        ]);
    }

    public function index()
    {
        return view('GameIndexPage', [
            'games'      => Game::query()->whereHas('authenticatedPlayer')->get(),
            'gameLogics' => GameLogic::get(),
        ]);
    }

    public function create(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (! $user->email) {
            throw new UnauthorizedHttpException();
        }

        $validator = Validator::make($request->all(), [
            'logic' => 'required|in:' . GameLogic::validationString(),
        ]);

        if ($validator->fails()) {
            throw new NotFoundHttpException();
        }

        /** @var Game $game */
        $game = Game::create([
            'token'            => Str::random(5),
            'logic_identifier' => $request->input('logic'),
            'host_user_id'     => $user->id,
        ]);

        $game->join();

        return redirect()->route('game.show', ['game' => $game]);
    }

    public function destroy(Game $game)
    {
        if (! $game->host_user_id !== Auth::id()) {
            throw new UnauthorizedHttpException();
        }

        $game->delete();

        return redirect()->route('game.index');
    }
}
