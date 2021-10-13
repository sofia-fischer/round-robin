<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaseModel extends Model
{
    use SoftDeletes;

    protected static function booted()
    {
        self::creating(function ($model): void {
            if (in_array('uuid', $model->getFillable()) && (! isset($model->attributes['uuid']) || empty($model->attributes['uuid']))) {
                $model->uuid = Str::uuid();
            }
        });
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

}
