<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use LEVELS\Analytics\Tracking\Queue\Events\CalculationQueued;

/**
 * Class Player
 *
 * Fillables
 *
 * @property  int id
 * @property  string uuid
 * @property  int group_id
 * @property  int user_id
 * @property  string name
 * @property  string color
 * @property  Carbon created_at
 * @property  Carbon updated_at
 * @property  Carbon deleted_at
 *
 * Attributes
 * @property string activeColor
 * @property string passiveColor
 *
 * @package app/Database/Models
 */
class Player extends BaseModel
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
        'agent',
        'group_id',
        'user_id',
        'name',
        'color',
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

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function moves()
    {
        return $this->hasMany(Move::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Attributes
    |--------------------------------------------------------------------------
    */

    protected function getActiveColorAttribute()
    {
        return ($this->color ?? 'pink') . '-500';
    }

    protected function getPassiveColorAttribute()
    {
        return ($this->color ?? 'pink') . '-100';
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

    public function scoreInGame($game)
    {
        $gameIds = Collection::wrap(is_numeric($game) ? $game : ($game->id ?? $game));

        return $this->moves()
            ->whereHas('round', function ($roundQuery) use ($gameIds) {
                return $roundQuery->whereIn('game_id', $gameIds);
            })
            ->sum('score');
    }
}
