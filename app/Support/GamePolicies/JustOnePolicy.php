<?php

namespace App\Support\GamePolicies;

use App\Models\Game;
use App\Models\Move;
use App\Models\Player;
use App\Models\Round;
use App\Queue\Events\GameRoundAction;
use App\Queue\Events\GameStarted;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class JustOnePolicy extends Policy
{
    public function startGame(Game $game)
    {
        if (!$game->started_at) {
            $game->started_at = now();
            $game->save();
        }

        Round::create([
            'uuid'             => Str::uuid(),
            'game_id'          => $game->id,
            'active_player_id' => $game->currentPlayer->id,
            'payload'          => [
                'word' => collect(config('just_one.words'))->random(),
            ],
        ]);
        event(new GameStarted($game->id));
    }

    public function playerJoined(Player $player, Game $game)
    {
        if (!$game->started_at) {
            $this->startGame($game);
        }
    }

    public function roundAction(Round $round, array $options = [])
    {
        /** @var Move $move */
        $move = Move::updateOrCreate([
            'round_id'  => $round->id,
            'player_id' => $round->game->authenticatedPlayer->id,
        ], [
            'user_id' => Auth::id(),
            'payload' => $options,
        ]);

        $this->checkForEndOfRound($round);
        event(new GameRoundAction($round->game_id));
    }

    private function checkForEndOfRound(Round $round)
    {
        if ($round->moves()->count() < $round->game->players()->count()) {
            return;
        }

        $moves = $round->moves()->where('player_id', '!=', $round->active_player_id)->get();

        $words = $moves->map(function (Move $move) {
            return Str::lower($move->payload['clue']);
        });

        // calculate reward points
        $moves->map(function (Move $move) use ($words, $round) {
            $moveWord = Str::lower($move->payload['clue']);

            $similarWords = $words->filter(function ($word) use ($moveWord, $round) {
                if ($moveWord == $word) {
                    return true;
                }

                if ($moveWord == $round->payload['move']) {
                    return true;
                }

                if (Str::contains($round->payload['move'], $moveWord) || Str::contains($moveWord, $round->payload['move'])) {
                    return true;
                }

                if (Str::contains($word, $moveWord) || Str::contains($moveWord, $word)) {
                    return true;
                }

                return false;
            });

            $move->payload['visible'] = $similarWords->count() > 1 ? false : true;
            $move->save();
        });

        $roundPayload = $round->payload;
        $roundPayload['clues_calculated'] = true;
        $round->payload = $roundPayload;
        $round->save();
        event(new GameRoundAction($round->game_id));
    }

    public function endRound(Round $round)
    {
        $guessedWord = Str::lower($round->authenticatedPlayerMove);
        $word = Str::lower($round->payload['word']);

        if ($guessedWord == $word || Str::contains($guessedWord, $word)) {
            $round->moves()->update(['score', 1]);
        }

        $this->startGame($round->game);
    }
}
