<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use LEVELS\Analytics\Tracking\Queue\Events\CalculationQueued;

/**
 * Class Move
 *
 * Fillables
 *
 * @property  int id
 * @property  string uuid
 * @property  int round_id
 * @property  int player_id
 * @property  int user_id
 * @property  int score
 * @property  array payload
 * @property  Carbon created_at
 * @property  Carbon updated_at
 * @property  Carbon deleted_at
 *
 * Relationships
 *
 * @property Round round
 * @property Player player
 *
 * Attributes
 *
 * @property bool authenticatedPlayerIsActive
 *
 * @package app/Database/Models
 */
class Move extends BaseModel
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
        'round_id',
        'player_id',
        'user_id',
        'score',
        'payload',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'         => 'int',
        'uuid'       => 'string',
        'round_id'   => 'int',
        'player_id'  => 'int',
        'user_id'    => 'int',
        'score'      => 'int',
        'payload'    => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function round()
    {
        return $this->belongsTo(Round::class, 'round_id');
    }

    public function player()
    {
        return $this->belongsTo(Player::class, 'player_id');
    }
}
