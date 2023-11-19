<?php

declare(strict_types=1);

namespace App\Http\Controllers;


use App\Http\Requests\JustOneMoveCreateRequest;
use App\Models\JustOneGame;
use App\Models\Move;
use App\Models\Player;
use App\Models\Round;
use App\Queue\Events\GameEnded;
use App\Queue\Events\GameRoundAction;
use App\Queue\Events\PlayerCreated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class JustOneController
{
    public function move(JustOneMoveCreateRequest $request, JustOneGame $game)
    {
        /** @var Move $move */
        $move = Move::updateOrCreate([
            'round_id' => $game->currentRound->id,
            'player_id' => $game->authenticatedPlayer->id,
        ], [
            'user_id' => Auth::id(),
        ]);

        $game->authenticatedPlayerIsActive
            ? $move->setPayloadWithKey('guess', $request->guess())
            : $move->setPayloadWithKey('clue', $request->clue());

        // Calculate clues
        if ($game->isWaitingForClue && $game->currentRound->moves->count() === ($game->players->count() - 1)) {
            $words = $game->currentRound->moves->map(fn (Move $move) => Str::upper($move->getPayloadWithKey('clue')));

            // calculate visibility of words
            $game->currentRound->moves->map(function (Move $move) use ($words) {
                $moveWord = Str::upper($move->getPayloadWithKey('clue'));
                $move->setPayloadWithKey('visible', $words->filter(fn ($word) => $word === $moveWord
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

    public function show(JustOneGame $game)
    {
        if ($game->authenticatedPlayer) {
            return view('GamePage', ['game' => $game]);
        }

        /** @var Player $player */
        $player = Player::query()->create([
            'user_id' => Auth::id(),
            'game_id' => $game->id,
        ]);
        event(new PlayerCreated($player));

        if (! $game->currentRound) {
            return $this->round($game);
        }

        return view('GamePage', ['game' => $game]);
    }

    public function round(JustOneGame $game)
    {
        $round = $game->currentRound;
        if ($round && ! $round->completed_at) {
            $round->update(['completed_at' => now()]);
        }

        Round::create([
            'game_id' => $game->id,
            'active_player_id' => $game->nextPlayer->id,
            'payload' => [
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
