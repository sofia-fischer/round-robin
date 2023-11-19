<?php

declare(strict_types=1);

namespace App\Models;

use App\ValueObjects\ColorEnum;
use Illuminate\Support\Carbon;

/**
 * Class Player
 *
 * Fillables
 *
 * @property  string $id
 * @property  string game_id
 * @property  string user_id
 * @property  array payload
 * @property  Carbon created_at
 * @property  Carbon updated_at
 * @property  Carbon deleted_at
 *
 * Relationships
 * @property \Illuminate\Support\Collection moves
 * @property \App\Models\Game game
 * @property \App\Models\User|null user
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
        'game_id',
        'user_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
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
        return ColorEnum::fromUuid($this->id)->baseColor();
    }

    protected function getPassiveColorAttribute()
    {
        return ColorEnum::fromUuid($this->id)->passiveColor();
    }

    public function color(): ColorEnum
    {
        return ColorEnum::fromUuid($this->id);
    }

    protected function getNameAttribute()
    {
        return $this->user?->name ?? ColorEnum::nameFromUuid($this->id);
    }

    protected function getScoreAttribute()
    {
        return $this->moves()->sum('score');
    }
}
