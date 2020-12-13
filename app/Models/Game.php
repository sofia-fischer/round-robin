<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use LEVELS\Analytics\Tracking\Queue\Events\CalculationQueued;

/**
 * Class Game
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

    public function players()
    {
        return $this->hasMany(Player::class);
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

}
