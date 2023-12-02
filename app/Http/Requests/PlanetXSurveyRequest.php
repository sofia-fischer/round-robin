<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\ValueObjects\Enums\PlanetXIconEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlanetXSurveyRequest extends FormRequest
{
    public function rules(): array
    {
        /** @var \App\Models\PlanetXGame $game */
        $game = $this->route('game');
        $current = $game->getCurrentNightSkyIndex();
        $visibleSky = array_map(fn ($sector) => $sector % 12, range($current, $current + 5));

        return [
            'icon' => ['string', 'required', Rule::in(PlanetXIconEnum::diff([PlanetXIconEnum::PLANET_X]))],
            'from' => ['int', 'required', 'min:0', 'max:11', Rule::in($visibleSky)],
            'to' => ['int', 'required', 'min:0', 'max:11', Rule::in($visibleSky)],
        ];
    }
}
