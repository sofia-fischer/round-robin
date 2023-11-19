<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Class Game
 *
 * Fillables
 *
 * @property  string $id
 * @property  string token
 * @property  string logic_identifier
 * @property  string host_user_id
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
 * @property \App\Models\Round currentRound
 * @see \App\Models\Game::currentRound()
 * @property \Illuminate\Support\Collection players
 * @see \App\Models\Game::players()
 * @property Player authenticatedPlayer
 * @see \App\Models\Game::authenticatedPlayer()
 * @property Player hostPlayer
 * @see \App\Models\Game::hostPlayer()
 * @property \App\Models\User hostUser
 * @see \App\Models\Game::hostUser()
 * @property \App\Models\Move authenticatedCurrentMove
 * @see \App\Models\Game::authenticatedCurrentMove()
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
        'token',
        'logic_identifier',
        'host_user_id',
    ];

    static $title = 'Undefined Game';
    static $description = 'Undefined Description';

    // A class that extends this game will cause Eloquent to generate a different table name
    protected $table = 'games';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
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

    public function authenticatedCurrentMove()
    {
        return $this->hasOneThrough(Move::class, Round::class, 'game_id', 'round_id')
            ->where('rounds.completed_at', null)
            ->where('moves.user_id', Auth::id());
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

    public function hostUser()
    {
        return $this->belongsTo(User::class, 'host_user_id');
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
        return $this->currentRound?->activePlayer ?? $this->hostPlayer;
    }

    protected function getNextPlayerAttribute()
    {
        if (! $this->currentRound) {
            return $this->hostPlayer;
        }

        $nextPlayer = $this->players->firstWhere('id', '>', $this->currentRound->active_player_id);

        return $nextPlayer ?? $this->hostPlayer;
    }

    protected function getAuthenticatedPlayerIsActiveAttribute()
    {
        return $this->currentPlayer->user_id === Auth::id();
    }

    protected function getAuthenticatedPlayerMoveAttribute()
    {
        return $this->currentRound ? $this->currentRound->authenticatedPlayerMove : null;
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

    public function addCurrentPayloadAttribute(string $key, mixed $value): array
    {
        return $this->currentRound->addPayloadAttribute($key, $value);
    }

    public function mergeCurrentPayloadAttribute(array $data): array
    {
        return $this->currentRound->mergePayloadAttribute($data);
    }

    public function authenticatedMovePayloadAttribute(string $key, $default = null)
    {
        if (! $this->authenticatedPlayerMove) {
            return $default;
        }

        return $this->authenticatedPlayerMove->getPayloadWithKey($key, $default);
    }

    /*
    |--------------------------------------------------------------------------
    | Boot
    |--------------------------------------------------------------------------
    */

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
