<?php

namespace App\Http\Requests;

use App\Models\JustOneGame;
use App\Models\WerewolfGame;
use App\Models\WaveLengthGame;
use Illuminate\Foundation\Http\FormRequest;

class GameCreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'logic' => [
                'required',
                'in:' . implode(',', [
                    WaveLengthGame::$logic_identifier,
                    WerewolfGame::$logic_identifier,
                    JustOneGame::$logic_identifier,
                ])
            ],
        ];
    }
}
