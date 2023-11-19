<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlanetXTargetRequest extends FormRequest
{
    public function rules(): array
    {
        /** @var \App\Models\PlanetXGame $game */
        $game = $this->route('game');
        $current = $game->getCurrentNightSkyIndex();
        $visibleSky = array_map(fn ($sector) => $sector % 12, range($current, $current + 5));

        return [
            'target' => ['int', 'required', 'min:0', 'max:11', Rule::in($visibleSky)],
        ];
    }
}
