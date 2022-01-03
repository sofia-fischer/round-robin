<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlayerUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'color' => ['nullable', 'string', 'in:gray,red,orange,yellow,green,teal,cyan,blue,purple,pink,'],
        ];
    }
}
