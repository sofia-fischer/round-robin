<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlanetXConferenceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'conference' => ['string', 'required', 'in:A,B,C,D,E,F'],
        ];
    }
}
