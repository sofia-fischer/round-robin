<?php

namespace App\Http\Requests;

use App\ValueObjects\Enums\PlanetXIconEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlanetXHintRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'section' => ['required', 'int', 'min:0', 'max:11'],
            'icon' => ['required', 'string', Rule::in(PlanetXIconEnum::values())],
            'value' => ['required', 'bool'],
        ];
    }
}
