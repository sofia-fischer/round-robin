<?php

namespace App\Models;

use Illuminate\Support\Carbon;
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
 * @property  int counter
 * @property  string color
 * @property  Carbon created_at
 * @property  Carbon updated_at
 * @property  Carbon deleted_at
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
        'counter',
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
