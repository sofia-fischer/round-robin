<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\WerewolfGame;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WerewolfVoteRequest extends FormRequest
{
    public function rules(): array
    {
        /** @var WerewolfGame $game */
        $game = $this->route('game');

        return ['vote' => ['required', Rule::in($game->players->pluck('id')->toArray())]];
    }
}
