<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use LEVELS\Analytics\Tracking\Queue\Events\CalculationQueued;

/**
 * Class GameLogic
 *
 * @package app/Database/Models
 */
class GameLogic extends Model
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
        'name',
        'start_logic',
        'round_logic',
        'win_logic',
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

    public function startGame()
    {
        $className = $this->start_logic;
        $calculator = app($className);

        return $calculator->handle();
    }

    public function checkForWin()
    {
        $className = $this->round_logic;
        $calculator = app($className);

        return $calculator->handle();
    }

    public function endRound()
    {
        $className = $this->win_logic;
        $calculator = app($className);

        return $calculator->handle();
    }
}
