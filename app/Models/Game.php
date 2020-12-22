<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
 *
 * Attributes
 *
 * @property Player currentPlayer
 * @property Player nextPlayer
 *
 * @package app/Database/Models
 */
class Game extends Model
{
    /*
    |--------------------------------------------------------------------------
    | General Table Information
    |--------------------------------------------------------------------------
    */

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'uuid',
        'game_logic_id',
        'group_id',
        'started_at',
        'ended_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

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

    public function startGame()
    {
        $className = $this->logic->policy;
        $logic = app($className);

        return $logic->startGame($this);
    }

    public function roundAction(array $options = null)
    {
        $className = $this->logic->policy;
        $logic = app($className);

        return $logic->roundAction($this->currentRound, $options);
    }

    public function nextRound()
    {
        $className = $this->logic->policy;
        $logic = app($className);

        return $logic->nextRound($this->currentRound);
    }
}
