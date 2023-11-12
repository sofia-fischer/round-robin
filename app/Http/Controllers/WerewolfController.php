<?php


namespace App\Http\Controllers;


use App\Models\Move;
use Illuminate\Support\Str;
use App\Models\WerewolfGame;
use App\Queue\Events\PlayerCreated;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\WerewolfMoveCreateRequest;
use Illuminate\Auth\Access\AuthorizationException;

class WerewolfController
{
    public function move(WerewolfMoveCreateRequest $request, WerewolfGame $game)
    {
        /** @var Move $move */
        $move = Move::updateOrCreate([
            'round_id'  => $game->currentRound->id,
            'player_id' => $game->authenticatedPlayer->id,
            'user_id'   => Auth::id(),
        ], [
            'uuid' => Str::uuid(),
        ]);

        $move->setPayloadWithKey($request->payloadKey(), $request->payloadValue());

        return view('GamePage', ['game' => $game]);
    }

    public function show(WerewolfGame $game)
    {
        if ($game->authenticatedPlayer) {
            return view('GamePage', ['game' => $game]);
        }

        /** @var \App\Models\Player $player */
        $player = $game->players()->create([
            'user_id' => Auth::id(),
        ]);
        $game->refresh();

        if ($game->started_at) {
            $game->addCurrentPayloadAttribute('playerRoles', collect([$player->id => WerewolfGame::WATCHER])->union($game->playerRoles));
        }
        event(new PlayerCreated($player));

        return view('GamePage', ['game' => $game]);
    }

    public function sunrise(WerewolfGame $game)
    {
        throw_unless($game->host_user_id === Auth::id(), AuthorizationException::class);

        if ($game->isDay) {
            return view('GamePage', ['game' => $game]);
        }

        $game->sunrise();

        return view('GamePage', ['game' => $game]);
    }

    public function vote(WerewolfGame $game)
    {
        throw_unless($game->host_user_id === Auth::id(), AuthorizationException::class);

        if ($game->isNight) {
            return view('GamePage', ['game' => $game]);
        }

        $game->vote();

        return view('GamePage', ['game' => $game]);
    }

    public function round(WerewolfGame $game)
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
