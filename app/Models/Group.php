<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Class Group
 *
 * Fillables
 *
 * @property  int id
 * @property  string uuid
 * @property  int host_user_id
 * @property  string token
 * @property  Carbon created_at
 * @property  Carbon updated_at
 * @property  Carbon deleted_at
 *
 * Relationships
 * @property host
 * @property players
 * @property authenticatedPlayer
 * @property games
 *
 * @package App\Database\Models
 */
class Group extends BaseModel
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
        'host_user_id',
        'token',
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

    public function host()
    {
        return $this->belongsTo(User::class, 'host_user_id');
    }

    public function players()
    {
        return $this->hasMany(Player::class);
    }

    public function authenticatedPlayer()
    {
        return $this->hasOne(Player::class)
            ->whereNotNull('user_id')
            ->where('user_id', Auth::id());
    }

    public function games()
    {
        return $this->hasMany(Game::class);
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

    /*
    |--------------------------------------------------------------------------
    | Boot
    |--------------------------------------------------------------------------
    */

    public static function boot()
    {
        parent::boot();

        self::created(function ($round) {
            $round->token = Str::upper(Str::random(6));
            $round->save();
        });
    }
}
