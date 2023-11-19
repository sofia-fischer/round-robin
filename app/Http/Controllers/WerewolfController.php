<?php

declare(strict_types=1);

namespace App\Http\Controllers;


use App\Http\Requests\WerewolfMoveCreateRequest;
use App\Http\Requests\WerewolfVoteRequest;
use App\Models\Move;
use App\Models\WerewolfGame;
use App\Queue\Events\PlayerCreated;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;

class WerewolfController
{
    public function move(WerewolfMoveCreateRequest $request, WerewolfGame $game)
    {
        /** @var Move $move */
        $move = Move::updateOrCreate([
            'round_id' => $game->currentRound->id,
            'player_id' => $game->authenticatedPlayer->id,
            'user_id' => Auth::id(),
        ]);

        $move->setPayloadWithKey('move', $request->validated());

        return view('GamePage', ['game' => $game]);
    }

    public function vote(WerewolfVoteRequest $request, WerewolfGame $game)
    {
        /** @var Move $move */
        $move = Move::updateOrCreate([
            'round_id' => $game->currentRound->id,
            'player_id' => $game->authenticatedPlayer->id,
            'user_id' => Auth::id(),
        ]);

        $move->setPayloadWithKey('vote', $request->get('vote'));

        return view('GamePage', ['game' => $game]);
    }

    public function show(WerewolfGame $game)
    {
        if ($game->authenticatedPlayer) {
            return view('GamePage', ['game' => $game]);
        }

        /** @var \App\Models\Player $player */
        $player = $game->players()->create(['user_id' => Auth::id()]);
        $game->refresh();

        event(new PlayerCreated($player));

        return view('GamePage', ['game' => $game]);
    }

    public function sunrise(WerewolfGame $game)
    {
        throw_unless($game->host_user_id === Auth::id(), AuthorizationException::class);

        $board = $game->getCurrentWerewolfBoard();
        if (! $board->isNight()) {
            return view('GamePage', ['game' => $game]);
        }

        $game->sunrise();

        return view('GamePage', ['game' => $game]);
    }

    public function end(WerewolfGame $game)
    {
        throw_unless($game->host_user_id === Auth::id(), AuthorizationException::class);

        $board = $game->getCurrentWerewolfBoard();
        if (! $board->isNight()) {
            return view('GamePage', ['game' => $game]);
        }

        $game->end();

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
