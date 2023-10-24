<?php

namespace App\Http\Requests;

use App\Models\PlanetXGame;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlanetXHintRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'section' => ['required', 'int', 'min:0', 'max:11'],
            'icon' => [
                'required',
                'string',
                Rule::in([
                    PlanetXGame::PLANET,
                    PlanetXGame::PLANET_X,
                    PlanetXGame::COMET,
                    PlanetXGame::GALAXY,
                    PlanetXGame::MOON,
                    PlanetXGame::EMPTY_SPACE,
                ]),
            ],
            'value' => ['required', 'bool'],
        ];
    }
}
