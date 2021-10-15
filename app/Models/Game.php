<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use App\Queue\Events\PlayerCreated;
use Illuminate\Support\Facades\Auth;
use App\Queue\Events\PlayerDestroyed;
use LEVELS\Analytics\Tracking\Queue\Events\CalculationQueued;

/**
 * Class Game
 *
 * Fillables
 *
 * @property  int id
 * @property  string uuid
 * @property  string token
 * @property  string logic_identifier
 * @property  int host_user_id
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
 * @property Player authenticatedPlayer
 *
 * Attributes
 *
 * @property Player currentPlayer
 * @property Player nextPlayer
 * @property bool authenticatedPlayerIsActive
 * @property Move authenticatedPlayerMove
 *
 * @property \App\Support\Interfaces\Logic $logic
 * @see \App\Models\Game::getLogicAttribute()
 *
 * @package app/Database/Models
 */
class Game extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'token',
        'logic_identifier',
        'host_user_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'           => 'int',
        'uuid'         => 'string',
        'host_user_id' => 'int',
        'started_at'   => 'datetime',
        'ended_at'     => 'datetime',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'deleted_at'   => 'datetime',
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
        return $this->hasMany(Player::class);
    }

    public function authenticatedPlayer()
    {
        return $this->hasOne(Player::class)->where('user_id', Auth::id());
    }

    /*
    |--------------------------------------------------------------------------
    | Attributes
    |--------------------------------------------------------------------------
    */

    protected function getCurrentPlayerAttribute()
    {
        $playersCount = $this->players()->count();

        return $this->players()
            ->latest()
            ->skip($playersCount > 1 ? $this->rounds()->count() % ($playersCount - 1) : 0)
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

    protected function getLogicAttribute()
    {
        return app($this->logic_identifier);
    }

    /*
    |--------------------------------------------------------------------------
    | Capabilities
    |--------------------------------------------------------------------------
    */

    public function start()
    {
        $this->logic->startGame($this);
    }

    public function join(): Player
    {
        /** @var Player $player */
        $player = $this->players()->create(['user_id' => Auth::id()]);
        event(new PlayerCreated($player->id));
        $this->logic->playerJoined($player, $this);

        return $player;
    }

    public function destroyPlayer(Player $player): bool
    {
        $player->delete();
        event(new PlayerDestroyed($player->id));

        $this->logic->playerJoined($player, $this);

        return true;
    }

    public function roundAction(array $options = null)
    {
        $this->logic->roundAction($this->currentRound, $options);
    }

    public function endRound()
    {
        $this->logic->endRound($this->currentRound);
    }

    /**
     *  Setup model event hooks
     */
    public static function boot()
    {
        parent::boot();

        self::deleting(function (Game $game) {
            $game->players()->delete();
            $game->rounds()->delete();
        });
    }
}
