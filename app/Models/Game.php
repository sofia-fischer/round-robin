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
 * @see \App\Models\Game::rounds()
 * @property Round currentRound
 * @see \App\Models\Game::currentRound()
 * @property \Illuminate\Support\Collection players
 * @see \App\Models\Game::players()
 * @property Player authenticatedPlayer
 * @see \App\Models\Game::authenticatedPlayer()
 * @property Player hostPlayer
 * @see \App\Models\Game::hostPlayer()
 *
 * Attributes
 *
 * @property Player currentPlayer
 * @see \App\Models\Game::getCurrentPlayerAttribute()
 * @property Player nextPlayer
 * @see \App\Models\Game::getNextPlayerAttribute()
 * @property bool authenticatedPlayerIsActive
 * @see \App\Models\Game::getAuthenticatedPlayerIsActiveAttribute()
 * @property Move authenticatedPlayerMove
 * @see \App\Models\Game::getAuthenticatedPlayerMoveAttribute()
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
        return $this->hasMany(Round::class, 'game_id');
    }

    public function moves()
    {
        return $this->hasManyThrough(Move::class, Round::class);
    }

    public function currentRound()
    {
        return $this->hasOne(Round::class, 'game_id')->latest();
    }

    public function players()
    {
        return $this->hasMany(Player::class, 'game_id');
    }

    public function hostPlayer()
    {
        return $this->hasOne(Player::class, 'game_id')->where('user_id', $this->host_user_id);
    }

    public function authenticatedPlayer()
    {
        return $this->hasOne(Player::class, 'game_id')->where('user_id', Auth::id());
    }

    /*
    |--------------------------------------------------------------------------
    | Attributes
    |--------------------------------------------------------------------------
    */

    protected function getCurrentPlayerAttribute()
    {
        if (! $this->currentRound) {
            return $this->hostPlayer;
        }

        return $this->currentRound->activePlayer;
    }

    protected function getNextPlayerAttribute()
    {
        if (! $this->currentRound) {
            return $this->hostPlayer;
        }

        $nextPlayer = $this->players()
            ->where('id', '>', $this->currentRound->active_player_id)
            ->first();

        return $nextPlayer ?? $this->hostPlayer;
    }

    protected function getAuthenticatedPlayerIsActiveAttribute()
    {
        return $this->currentPlayer->user_id == Auth::id();
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
        if ($this->authenticatedPlayer) {
            return $this->authenticatedPlayer;
        }

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

    public function roundAction(array $options = [])
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

    /*
    |--------------------------------------------------------------------------
    | Capabilities
    |--------------------------------------------------------------------------
    */

    public function currentPayloadAttribute(string $key, $default = null)
    {
        if (! $this->currentRound) {
            return $default;
        }

        return $this->currentRound->payloadAttribute($key, $default);
    }
}
