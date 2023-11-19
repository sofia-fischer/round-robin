<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'token' => ['nullable', 'string', Rule::exists(Game::class, 'token')],
            'name' => ['required', 'string', 'max:255', Rule::exists(User::class, 'name')],
            'password' => ['required', 'string', 'min:7'],
        ];
    }
}
