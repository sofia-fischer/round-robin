<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Queue\Events\GameEnded;
use App\Queue\Events\GameStarted;
use App\Queue\Events\PlayerCreated;
use Illuminate\Support\Facades\Auth;
use App\Queue\Events\GameRoundAction;
use App\Models\Builders\B2BUserBuilder;
use Illuminate\Database\Eloquent\Builder;
use LEVELS\Analytics\Tracking\Queue\Events\CalculationQueued;

/**
 * Class Game
 *
 * @property int $waveLength
 * @property string $clue
 *
 * @package App\Models
 */
class WaveLengthGame extends Game
{
    protected $table = 'games';
    static $logic_identifier = App\Support\GameLogics\WavelengthLogic::class;

    public static function query(): Builder
    {
        return parent::query()
            ->where('logic_identifier', self::$logic_identifier);
    }

    public function start()
    {
        if (! $this->started_at) {
            $this->started_at = now();
            $this->save();
            event(new GameStarted($this->id));
        }

        Round::create([
            'uuid'             => Str::uuid(),
            'game_id'          => $this->id,
            'active_player_id' => $this->currentPlayer->id,
            'payload'          => [
                'waveLength' => random_int(0, 100),
                'antonyms'   => collect($this->antonyms)->random(),
            ],
        ]);
        event(new GameRoundAction($this->id));
    }

    public function join(): Player
    {
        /** @var Player $player */
        $player = $this->players()->create(['user_id' => Auth::id()]);
        event(new PlayerCreated($player->id));

        if (! $this->started_at) {
            $this->start($this);
        }

        return $player;
    }

    public function roundAction(array $options = [])
    {
        $round = $this->currentRound;

        /** @var Move $move */
        $move = Move::updateOrCreate([
            'round_id'  => $round->id,
            'player_id' => $this->authenticatedPlayer->id,
            'user_id'   => Auth::id(),
        ], [
            'uuid' => Str::uuid(),
        ]);

        if ($round->authenticatedPlayerIsActive) {
            $payload         = $round->payload ?? [];
            $payload['clue'] = $options['clue'];
            $round->payload  = $payload;
            $round->save();
            event(new GameRoundAction($this->id));

            return;
        }

        $move->payload = ['guess' => $options['guess']];
        $move->save();

        $this->checkForEndOfRound();
        event(new GameRoundAction($this->id));
    }

    private function checkForEndOfRound()
    {
        $round = $this->currentRound;

        if ($round->moves()->count() < $this->players()->count()) {
            return;
        }

        $target = $round->payload['waveLength'];

        // calculate reward points
        $round->moves()->where('player_id', '!=', $round->active_player_id)->get()->map(function (Move $move) use ($target) {
            $diffFromTarget = abs($target - $move->payload['guess']);
            $reward         = 0;

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
        $activePlayerMove        = $round->moves()->where('player_id', $round->active_player_id)->first();
        $activePlayerMove->score = ceil($round->moves()->where('player_id', '!=', $round->active_player_id)->average('score'));
        $activePlayerMove->save();

        $round->completed_at = now();
        $round->save();
        event(new GameEnded($this->id));
    }

    public function endRound()
    {
        $this->start();
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
        ['Log Level: Debug' => 'Log Level: Critical'],
        ['😇' => '😏'],
    ];

    public $title = 'Wavelength';

    static $description = 'The active Player knows where the target on a spectrum between two opposing concepts is,
            but can only give a verbal clue to the other players, who only see the opposing concepts.
            With that clue, the other players have to guess where the target is.';
}
