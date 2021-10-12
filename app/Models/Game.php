<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use LEVELS\Analytics\Tracking\Queue\Events\CalculationQueued;

/**
 * Class Game
 *
 * Fillables
 *
 * @property  int id
 * @property  string uuid
 * @property  int game_logic_id
 * @property  int group_id
 * @property  Carbon started_at
 * @property  Carbon ended_at
 * @property  Carbon created_at
 * @property  Carbon updated_at
 * @property  Carbon deleted_at
 *
 * Relationships
 *
 * @property \Illuminate\Support\Collection rounds
 * @property Round currentRound
 * @property \Illuminate\Support\Collection players
 * @property GameLogic logic
 * @property Player authenticatedPlayer
 * @property Group group
 *
 * Attributes
 *
 * @property Player currentPlayer
 * @property Player nextPlayer
 * @property bool authenticatedPlayerIsActive
 * @property Move authenticatedPlayerMove
 *
 * @package app/Database/Models
 */
class Game extends BaseModel
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'            => 'int',
        'game_logic_id' => 'int',
        'group_id'      => 'int',
        'started_at'    => 'dateTime',
        'ended_at'      => 'dateTime',
        'created_at'    => 'dateTime',
        'updated_at'    => 'dateTime',
        'deleted_at'    => 'dateTime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function rounds()
    {
        return $this->hasMany(Round::class);
    }

    public function currentRound()
    {
        return $this->hasOne(Round::class)->latest();
    }

    public function players()
    {
        return $this->hasMany(Player::class, 'group_id', 'group_id');
    }

    public function authenticatedPlayer()
    {
        return $this->hasOne(Player::class, 'group_id', 'group_id')->where('user_id', Auth::id());
    }

    public function logic()
    {
        return $this->belongsTo(GameLogic::class, 'game_logic_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Attributes
    |--------------------------------------------------------------------------
    */

    protected function getCurrentPlayerAttribute()
    {
        return $this->players()
            ->latest()
            ->skip($this->rounds()->count() % $this->players()->count())
            ->first();
    }

    protected function getNextPlayerAttribute()
    {
        return $this->players()
            ->latest()
            ->skip(($this->rounds()->count() + 1) % $this->players()->count())
            ->first();
    }

    protected function getAuthenticatedPlayerIsActiveAttribute()
    {
        return $this->currentRound->activePlayer->user_id == Auth::id();
    }

    protected function getAuthenticatedPlayerMoveAttribute()
    {
        return $this->currentRound ? $this->currentRound->authenticatedPlayerMove : null;
    }
    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | Capabilities
    |--------------------------------------------------------------------------
    */

    public function start()
    {
        $className = $this->logic->policy;
        $logic = app($className);

        $logic->startGame($this);
    }

    public function join(Player $player)
    {
        $className = $this->logic->policy;
        $logic = app($className);

        $logic->playerJoined($player, $this);
    }

    public function roundAction(array $options = null)
    {
        $className = $this->logic->policy;
        $logic = app($className);

        $logic->roundAction($this->currentRound, $options);
    }

    public function endRound()
    {
        $className = $this->logic->policy;
        $logic = app($className);

        $logic->endRound($this->currentRound);
    }
}
