<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WavelengthMoveCreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'clue'  => ['required_without:guess', 'string', 'alpha_dash'],
            'guess' => ['required_without:clue', 'integer', 'min:0', 'max:100'],
        ];
    }
}
