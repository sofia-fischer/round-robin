<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Support\Carbon;

/**
 * Class Move
 *
 * Fillables
 *
 * @property  string $id
 * @property  string round_id
 * @property  string player_id
 * @property  string user_id
 * @property  int score
 * @property  array payload
 * @property  Carbon created_at
 * @property  Carbon updated_at
 * @property  Carbon deleted_at
 *
 * Relationships
 *
 * @property Round round
 * @property Player player
 *
 * Attributes
 *
 * @property bool authenticatedPlayerIsActive
 *
 * @package app/Database/Models
 */
class Move extends BaseModel
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
        'uuid',
        'round_id',
        'player_id',
        'user_id',
        'score',
        'payload',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'score' => 'int',
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

    public function round()
    {
        return $this->belongsTo(Round::class, 'round_id');
    }

    public function player()
    {
        return $this->belongsTo(Player::class, 'player_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Capabilities
    |--------------------------------------------------------------------------
    */

    public function getPayloadWithKey(string $key, $default = null): mixed
    {
        return $this->payload[$key] ?? $default;
    }

    public function setPayloadWithKey(string $key, mixed $value): array
    {
        if (! (is_scalar($value) || is_null($value) || is_array($value))) {
            throw new \InvalidArgumentException('Value must be scalar, null, or array');
        }

        $payload = $this->payload ?? [];
        $payload[$key] = $value;
        $this->payload = $payload;
        $this->save();

        return $this->payload;
    }

    public function mergePayload(array $data): array
    {
        $this->payload = array_merge($this->payload ?? [], $data);
        $this->save();

        return $this->payload;
    }
}
