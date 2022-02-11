<?php

namespace App\Support\GameLogics;

use App\Models\Game;
use App\Models\Move;
use App\Models\Round;
use App\Models\Player;
use Illuminate\Support\Str;
use App\Queue\Events\GameStarted;
use App\Support\Interfaces\Logic;
use Illuminate\Support\Facades\Auth;
use App\Queue\Events\GameRoundAction;
use Faker\Generator;

class JustOneLogic implements Logic
{
    public function startGame(Game $game)
    {
        if (! $game->started_at) {
            $game->started_at = now();
            $game->save();
        }

        $faker = new Generator();
        Round::create([
            'uuid'             => Str::uuid(),
            'game_id'          => $game->id,
            'active_player_id' => $game->currentPlayer->id,
            'payload'          => [
                'word' => $faker->word(),
            ],
        ]);
        event(new GameStarted($game->id));
    }

    public function playerJoined(Player $player, Game $game)
    {
        if (! $game->started_at) {
            $this->startGame($game);
        }
    }

    public function roundAction(Round $round, array $options = [])
    {
    }

    static function giveClue(Round $round, string $clue)
    {
        /** @var Move $move */
        $move = Move::updateOrCreate([
            'round_id'  => $round->id,
            'player_id' => $round->game->authenticatedPlayer->id,
        ], [
            'user_id' => Auth::id(),
            'payload' => ['clue' => $clue],
        ]);

        event(new GameRoundAction($round->game));

        if ($round->moves()->count() < ($round->game->players()->count() - 1)) {
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

            $movePayload = $move->payload ?? [];
            $movePayload['visible'] = $similarWords->count() > 1 ? false : true;
            $move->payload = $movePayload;
            $move->save();
        });

        $roundPayload = $round->payload ?? [];
        $roundPayload['clues_calculated'] = true;
        $round->payload = $roundPayload;
        $round->save();

        event(new GameRoundAction($round->game));
    }

    public function endRound(Round $round)
    {
    }

    static function nextRound(Round $round)
    {
        Round::create([
            'uuid'             => Str::uuid(),
            'game_id'          => $round->game->id,
            'active_player_id' => $round->game->currentPlayer->id,
            'payload'          => [
                'word' => collect(config('just_one.words'))->random(),
            ],
        ]);
        event(new GameStarted($round->game->id));
    }

    static function giveGuess(Round $round, string $guessedWord)
    {
        $word = Str::lower($round->payload['word']);
        $guessedWord = Str::lower($guessedWord);

        /** @var Move $move */
        $move = Move::updateOrCreate([
            'round_id'  => $round->id,
            'player_id' => $round->game->authenticatedPlayer->id,
        ], [
            'user_id' => Auth::id(),
            'payload' => ['guess' => $guessedWord],
        ]);

        if ($guessedWord == $word || Str::contains($guessedWord, $word)) {
            $round->moves()->update(['score' => 1]);
        }

        $round->completed_at = now();
        $round->save();
    }

    static function title(): string
    {
        return 'Just One';
    }

    static function description(): string
    {
        return 'All players but the active player see the same word.
            Without communicating with each other each player but the active player gives a one worded clue.
            After all players made their clue, all clues which are a substring of the original word or duplicated words are hidden.
            The active player then has to guess the word using the visible clues.';
    }
}
