<?php


namespace App\Http\Controllers;


use App\Models\Move;
use App\Models\Round;
use App\Models\Player;
use Illuminate\Support\Str;
use App\Models\JustOneGame;
use App\Queue\Events\GameEnded;
use App\Queue\Events\PlayerCreated;
use Illuminate\Support\Facades\Auth;
use App\Queue\Events\GameRoundAction;
use App\Http\Requests\JustOneMoveCreateRequest;
use Illuminate\Auth\Access\AuthorizationException;

class JustOneController
{
    public function move(JustOneMoveCreateRequest $request, JustOneGame $game)
    {
        throw_if(! $game->authenticatedPlayerIsActive && ! $request->clue(), AuthorizationException::class);
        throw_if($game->authenticatedPlayerIsActive && ! $request->guess(), AuthorizationException::class);

        /** @var Move $move */
        $move = Move::updateOrCreate([
            'round_id'  => $game->currentRound->id,
            'player_id' => $game->authenticatedPlayer->id,
        ], [
            'user_id' => Auth::id(),
            'uuid'    => Str::uuid(),
        ]);

        $game->authenticatedPlayerIsActive
            ? $move->addPayloadAttribute('guess', $request->guess())
            : $move->addPayloadAttribute('clue', $request->clue());

        // Calculate clues
        if ($game->isWaitingForClue && $game->currentRound->moves->count() === ($game->players->count() - 1)) {
            $words = $game->currentRound->moves->map(fn (Move $move) => Str::upper($move->payloadAttribute('clue')));

            // calculate visibility of words
            $game->currentRound->moves->map(function (Move $move) use ($words) {
                $moveWord = Str::upper($move->payloadAttribute('clue'));
                $move->addPayloadAttribute('visible', $words->filter(fn ($word) => $word === $moveWord
                        || Str::contains($word, $moveWord)
                        || Str::contains($moveWord, $word))->count() === 1);
            });

            $game->addCurrentPayloadAttribute('clues_calculated', true);
            event(new GameRoundAction($game));

            return view('GamePage', ['game' => $game]);
        }

        // calculate results and end round
        if ($request->guess()) {
            // end round
            if (Str::upper($request->guess()) === Str::upper($game->word) || Str::contains(Str::upper($request->guess()), Str::upper($game->word))) {
                $game->currentRound->moves()->update(['score' => 1]);
            }

            $game->currentRound->completed_at = now();
            $game->currentRound->save();

            event(new GameEnded($game->currentRound->game_id));

            return view('GamePage', ['game' => $game]);
        }

        // continue with game without changing
        event(new GameRoundAction($game));

        return view('GamePage', ['game' => $game]);
    }

    public function show(JustOneGame $game, \Faker\Generator $generator)
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

        if (! $game->currentRound) {
            return $this->round($game);
        }

        return view('GamePage', ['game' => $game]);
    }

    public function round(JustOneGame $game)
    {
        if ($game->currentRound && ! $game->currentRound?->completed_at) {
            $game->currentRound->completed_at = now();
            $game->currentRound->save();
        }

        Round::create([
            'uuid'             => Str::uuid(),
            'game_id'          => $game->id,
            'active_player_id' => $game->nextPlayer->id,
            'payload'          => [
                'word' => collect(__('words'))->random(),
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
