<?php


namespace App\Http\Controllers;


use App\Models\Move;
use App\Models\Round;
use Illuminate\Support\Str;
use App\Models\WaveLengthGame;
use App\Queue\Events\GameEnded;
use App\Queue\Events\PlayerCreated;
use Illuminate\Support\Facades\Auth;
use App\Queue\Events\GameRoundAction;
use App\Http\Requests\WavelengthMoveCreateRequest;
use Illuminate\Auth\Access\AuthorizationException;

class WavelengthController
{
    public function move(WavelengthMoveCreateRequest $request, WaveLengthGame $game)
    {
        throw_if($game->authenticatedPlayerIsActive && ! $request->clue(), AuthorizationException::class);
        throw_if(! $game->authenticatedPlayerIsActive && ! $request->guess(), AuthorizationException::class);

        /** @var Move $move */
        $move = Move::updateOrCreate([
            'round_id'  => $game->currentRound->id,
            'player_id' => $game->authenticatedPlayer->id,
            'user_id'   => Auth::id(),
        ], [
            'uuid' => Str::uuid(),
        ]);

        $game->authenticatedPlayerIsActive
            ? $game->currentRound->addPayloadAttribute('clue', $request->clue())
            : $move->addPayloadAttribute('guess', $request->guess());

        // If current Round does not end
        if ($game->currentRound->moves()->count() < $game->players()->count()) {
            event(new GameRoundAction($game));

            return view('GamePage', ['game' => $game]);
        }

        // end current round
        $target = $game->currentPayloadAttribute('waveLength');

        // calculate reward points
        $scores = $game->currentRound->moves()
            ->where('player_id', '!=', $game->currentRound->active_player_id)
            ->get()
            ->map(function (Move $move) use ($target) {
                $diffFromTarget = abs($target - $move->payloadAttribute('guess'));

                $move->score = match (true) {
                    $diffFromTarget <= 5 => 10,
                    $diffFromTarget <= 10 => 3,
                    $diffFromTarget <= 20 => 1,
                    default => 0,
                };

                $move->save();

                return $move->score;
            });

        // reward active player
        /** @var Move $activePlayerMove */
        $activePlayerMove        = $game->currentRound->moves()->where('player_id', $game->currentRound->active_player_id)->first();
        $activePlayerMove->score = ceil($scores->average());
        $activePlayerMove->save();

        $game->currentRound->completed_at = now();
        $game->currentRound->save();

        event(new GameEnded($game->id));

        return view('GamePage', ['game' => $game]);
    }

    public function join(WaveLengthGame $game)
    {
        if ($game->authenticatedPlayer) {
            return view('GamePage', ['game' => $game]);
        }

        /** @var Player $player */
        $player = $game->players()->create([
            'uuid'    => Str::uuid(),
            'user_id' => Auth::id(),
        ]);
        event(new PlayerCreated($player));

        if (! $game->started_at) {
            return $this->round($game);
        }

        return view('GamePage', ['game' => $game]);
    }

    public function round(WaveLengthGame $game)
    {
        if ($game->currentRound && ! $game->currentRound->completed_at) {
            $game->currentRound->completed_at = now();
            $game->currentRound->save();
        }

        $antonym = collect(__('antonyms'))->random();
        Round::create([
            'uuid'             => Str::uuid(),
            'game_id'          => $game->id,
            'active_player_id' => $game->nextPlayer->id,
            'payload'          => [
                'waveLength' => random_int(0, 100),
                'antonym1'   => key($antonym),
                'antonym2'   => $antonym[key($antonym)],
            ],
        ]);

        if (! $game->started_at) {
            $game->started_at = now();
            $game->save();
        }

        event(new GameRoundAction($game));
        $game->refresh();

        return view('GamePage', ['game' => $game]);
    }
}
