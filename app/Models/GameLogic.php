<?php

namespace App\Models;

use LEVELS\Analytics\Tracking\Queue\Events\CalculationQueued;

/**
 * Class GameLogic
 *
 * @package app/Database/Models
 */
class GameLogic extends BaseModel
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id'            => 'int',
        'created_at'    => 'dateTime',
        'updated_at'    => 'dateTime',
        'deleted_at'    => 'dateTime',
    ];
}
