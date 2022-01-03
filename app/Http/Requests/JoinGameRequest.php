<?php

namespace App\Http\Requests;

use App\Models\Game;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Http\FormRequest;

class JoinGameRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'token' => ['required', 'string', Rule::exists(Game::class, 'token')],
        ];
    }
}
