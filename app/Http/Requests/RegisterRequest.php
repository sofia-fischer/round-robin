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
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'password' => ['required', 'string', 'min:7'],
        ];
    }

    public function data(): array
    {
        return [
            'name'              => $this->input('name'),
            'email'             => $this->input('email'),
            'email_verified_at' => now(),
            'password'          => Hash::make($this->input('password')),
        ];
    }
}
