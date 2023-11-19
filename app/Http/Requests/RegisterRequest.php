<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Game;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'token' => ['nullable', 'string', Rule::exists(Game::class, 'token')],
            'name' => ['required', 'string', 'max:255', Rule::unique(User::class, 'name')],
            'password' => ['required', 'string', 'min:7'],
        ];
    }

    public function data(): array
    {
        return [
            'name' => $this->input('name'),
            'password' => Hash::make($this->input('password')),
        ];
    }
}
