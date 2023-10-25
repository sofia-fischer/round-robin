<?php


namespace App\Http\Controllers;


use App\Http\Requests\PlanetXHintRequest;
use App\Http\Requests\WerewolfMoveCreateRequest;
use App\Models\Move;
use App\Models\PlanetXGame;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PlanetXController
{
    public function move(WerewolfMoveCreateRequest $request, PlanetXGame $game)
    {
        /** @var Move $move */
        $move = Move::updateOrCreate([
            'round_id' => $game->currentRound->id,
            'player_id' => $game->authenticatedPlayer->id,
            'user_id' => Auth::id(),
        ], [
            'uuid' => Str::uuid(),
        ]);

        $move->addPayloadAttribute($request->payloadKey(), $request->payloadValue());

        return view('GamePage', ['game' => $game]);
    }

    public function hint(PlanetXHintRequest $request, PlanetXGame $game)
    {
        $board = $request->getBoard();
        $game->storeAuthenticatedPlayerBoard($board);

        return view('GamePage', ['game' => $game]);
    }

    public function show(PlanetXGame $game)
    {
        if ($game->authenticatedPlayer) {
            return view('GamePage', ['game' => $game]);
        }

        /** @var \App\Models\Player $player */
        $player = $game->players()->create([
            'uuid' => Str::uuid(),
            'user_id' => Auth::id(),
            'game_id' => $game->id,
        ]);
        $game->refresh();

        return view('GamePage', ['game' => $game]);
    }

    public function round(PlanetXGame $game)
    {
        throw_unless($game->host_user_id === Auth::id(), AuthorizationException::class);

        if (! $game->started_at) {
            $game->started_at = now();
            $game->save();
        }

        if ($game->currentRound && ! $game->currentRound->completed_at) {
            $game->currentRound->completed_at = now();
            $game->currentRound->save();
        }

        $game->startRound();

        return view('GamePage', ['game' => $game]);
    }
}
