<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Class Group
 *
 * @package App\Database\Models
 */
class Group extends Model
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

    public function host()
    {
        return $this->belongsTo(User::class, 'host_user_id');
    }

    public function player()
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

    public function currentGame()
    {
        return $this->hasOne(Game::class)
            ->whereNull('ended_at')
            ->latest();
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
