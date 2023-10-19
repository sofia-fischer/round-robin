<?php

namespace App\Http\Requests;

use App\Models\Game;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'token'    => ['nullable', 'string', Rule::exists(Game::class, 'token')],
            'name'     => ['required', 'string', 'max:255', Rule::unique(User::class, 'name')],
            'password' => ['required', 'string', 'min:7'],
        ];
    }

    public function data(): array
    {
        return [
            'name'              => $this->input('name'),
            'password'          => Hash::make($this->input('password')),
        ];
    }
}
