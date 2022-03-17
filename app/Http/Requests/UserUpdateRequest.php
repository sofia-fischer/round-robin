<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'color'   => ['nullable', 'string', 'in:gray,red,orange,yellow,green,teal,cyan,blue,purple,pink,'],
            'game_id' => ['nullable', 'exists:games,id'],
            'name'    => ['nullable', 'string'],
            'email'   => ['nullable', 'email'],
        ];
    }

    public function data(): array
    {
        return array_filter([
            'color'             => $this->input('color'),
            'name'              => $this->input('name'),
            'email'             => $this->input('email'),
        ], fn ($element) => $element !== null);
    }
}
