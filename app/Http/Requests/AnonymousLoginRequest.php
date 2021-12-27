<?php

namespace App\Http\Requests;

use App\Models\Game;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Http\FormRequest;

class AnonymousLoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'token' => ['required', 'string', Rule::exists(Game::class, 'token')],
            'name'  => ['required', 'string', 'max:255'],
        ];
    }

    public function data(): array
    {
        return [
            'name'     => $this->input('name'),
            'password' => Hash::make(Str::random(30)),
        ];
    }
}
