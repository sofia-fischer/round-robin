<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use LEVELS\Analytics\Tracking\Queue\Events\CalculationQueued;

/**
 * Class Round
 *
 * Fillables
 *
 * @property  int id
 * @property  string uuid
 * @property  int game_id
 * @property  int active_player_id
 * @property  array payload
 * @property  Carbon completed_at
 * @property  Carbon created_at
 * @property  Carbon updated_at
 * @property  Carbon deleted_at
 *
 * Relationships
 *
 * @property Game game
 * @property Player activePlayer
 * @property Move authenticatedPlayerMove
 * @property \Illuminate\Support\Collection moves
 *
 * @package app/Database/Models
 */
class Round extends BaseModel
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
        'uuid',
        'game_id',
        'active_player_id',
        'payload',
        'completed_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'               => 'int',
        'uuid'             => 'string',
        'game_id'          => 'int',
        'active_player_id' => 'int',
        'payload'          => 'array',
        'completed_at'     => 'datetime',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
        'deleted_at'       => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id');
    }

    public function activePlayer()
    {
        return $this->belongsTo(Player::class, 'active_player_id');
    }

    public function authenticatedPlayerMove()
    {
        return $this->hasOne(Move::class)->where('user_id', Auth::id());
    }

    public function moves()
    {
        return $this->hasMany(Move::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Capabilities
    |--------------------------------------------------------------------------
    */

    public function payloadAttribute(string $key, $default = null): mixed
    {
        return $this->payload[$key] ?? $default;
    }

    public function addPayloadAttribute(string $key, mixed $value): array
    {
        $payload       = $this->payload ?? [];
        $payload[$key] = $value;
        $this->payload = $payload;
        $this->save();

        return $this->payload;
    }
}
