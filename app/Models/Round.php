<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

/**
 * Class Round
 *
 * Fillables
 *
 * @property  string $id
 * @property  string game_id
 * @property  string active_player_id
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

    public function mergePayloadAttribute(array $data): array
    {
        $this->payload = array_merge($this->payload ?? [], $data);
        $this->save();

        return $this->payload;
    }
}
