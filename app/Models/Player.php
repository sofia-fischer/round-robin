<?php

namespace App\Models;

use App\ValueObjects\Color;
use Illuminate\Support\Carbon;

/**
 * Class Player
 *
 * Fillables
 *
 * @property  int id
 * @property  string uuid
 * @property  int game_id
 * @property  int user_id
 * @property  array payload
 * @property  Carbon created_at
 * @property  Carbon updated_at
 * @property  Carbon deleted_at
 *
 * Relationships
 * @property \Illuminate\Support\Collection moves
 * @property \App\Models\Game game
 * @property \App\Models\User user
 *
 * Attributes
 * @property string activeColor
 * @property string passiveColor
 * @property int score
 *
 * @package app/Database/Models
 */
class Player extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'game_id',
        'user_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'int',
        'uuid' => 'string',
        'game_id' => 'int',
        'user_id' => 'int',
        'payload' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function moves()
    {
        return $this->hasMany(Move::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Attributes
    |--------------------------------------------------------------------------
    */

    protected function getActiveColorAttribute()
    {
        return Color::fromInt($this->id)->baseColor();
    }

    protected function getPassiveColorAttribute()
    {
        return Color::fromInt($this->id)->passiveColor();
    }

    protected function color(): Color
    {
        return Color::fromInt($this->id);
    }

    protected function getNameAttribute()
    {
        return $this->user->name;
    }

    protected function getScoreAttribute()
    {
        return $this->moves()->sum('score');
    }
}
