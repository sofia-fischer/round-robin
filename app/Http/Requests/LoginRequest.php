<?php

namespace App\Http\Requests;

use App\Models\Game;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'token'    => ['nullable', 'string', Rule::exists(Game::class, 'token')],
            'email'    => ['required', 'string', 'email', 'max:255', Rule::exists(User::class, 'email')],
            'password' => ['required', 'string', 'min:7'],
        ];
    }
}
