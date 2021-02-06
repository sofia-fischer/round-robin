<?php

namespace App\Support\GamePolicies;

use App\Models\Game;
use App\Models\Move;
use App\Models\Player;
use App\Models\Round;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WavelengthPolicy extends Policy
{
    public function startGame(Game $game)
    {
        $game->started_at = now();
        $game->save();

        Round::create([
            'uuid'             => Str::uuid(),
            'game_id'          => $game->id,
            'active_player_id' => $game->currentPlayer->id,
            'payload'          => [
                'waveLength' => random_int(0, 100),
                'antonyms'   => collect($this->antonyms)->random(),
            ],
        ]);
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
            'user_id'   => Auth::id(),
        ], [
            'uuid' => Str::uuid(),
        ]);

        if ($round->authenticatedPlayerIsActive) {
            $payload = $round->payload ?? [];
            $payload['clue'] = $options['clue'];
            $round->payload = $payload;
            $round->save();

            return;
        }

        $move->payload = ['guess' => $options['guess']];
        $move->save();

        $this->checkForEndOfRound($round);
    }

    private function checkForEndOfRound(Round $round)
    {
        if ($round->moves()->count() < $round->game->players()->count()) {
            return;
        }

        $target = $round->payload['waveLength'];

        // calculate reward points
        $round->moves()->where('player_id', '!=', $round->active_player_id)->get()->map(function (Move $move) use ($target) {
            $diffFromTarget = abs($target - $move->payload['guess']);
            $reward = 0;

            switch (true) {
                case $diffFromTarget <= 5:
                    $reward = 10;
                    break;
                case $diffFromTarget <= 10:
                    $reward = 3;
                    break;
                case $diffFromTarget <= 20:
                    $reward = 1;
                    break;
            }

            $move->score = $reward;
            $move->save();
        });

        // reward active player
        $activePlayerMove = $round->moves()->where('player_id', $round->active_player_id)->first();
        $activePlayerMove->score = ceil($round->moves()->where('player_id', '!=', $round->active_player_id)->average('score'));

        $round->completed_at = now();
        $round->save();
    }

    public function endRound(Round $round)
    {
        Round::create([
            'uuid'             => Str::uuid(),
            'game_id'          => $round->game_id,
            'active_player_id' => $round->game->nextPlayer->id,
            'payload'          => [
                'waveLength' => random_int(0, 100),
                'antonyms'   => collect($this->antonyms)->random(),
            ],
        ]);
    }

    public $antonyms = [
        ['alive' => 'dead'],
        ['backward' => 'forward'],
        ['beautiful' => 'ugly'],
        ['big' => 'small'],
        ['blunt' => 'sharp'],
        ['boring' => 'interesting'],
        ['bright' => 'dark'],
        ['broad' => 'narrow'],
        ['clean' => 'dirty'],
        ['intelligent' => 'stupid'],
        ['closed' => 'open'],
        ['cool' => 'warm'],
        ['cruel' => 'kind'],
        ['dangerous' => 'safe'],
        ['dark' => 'light'],
        ['deep' => 'shallow'],
        ['difficult' => 'easy'],
        ['dry' => 'wet'],
        ['early' => 'late'],
        ['fake' => 'real'],
        ['fast' => 'slow'],
        ['flexible' => 'inflexible'],
        ['gentle' => 'fierce'],
        ['good' => 'bad'],
        ['happy' => 'sad'],
        ['hard' => 'soft'],
        ['heavy' => 'light'],
        ['high' => 'low'],
        ['hot' => 'cold'],
        ['ill' => 'well'],
        ['innocent' => 'guilty'],
        ['long' => 'short'],
        ['loose' => 'tight'],
        ['loud' => 'soft'],
        ['low' => 'high'],
        ['modern' => 'ancient'],
        ['noisy' => 'quiet'],
        ['normal' => 'strange'],
        ['useful invention' => 'useless invention'],
        ['old' => 'new'],
        ['outgoing' => 'shy'],
        ['poor' => 'rich'],
        ['moral' => 'cruel'],
        ['rough' => 'smooth'],
        ['short' => 'tall'],
        ['sour' => 'sweet'],
        ['strong' => 'weak'],
        ['terrible' => 'wonderful'],
        ['far' => 'near'],
        ['cheap' => 'expensive'],
        ['low quality' => 'high quality'],
        ['normal greeting' => 'weird greeting'],
        ['bad advice' => 'good advice'],
        ['possible during Corona' => 'not possible during Corona'],
        ['😇' => '😏'],
    ];
}
