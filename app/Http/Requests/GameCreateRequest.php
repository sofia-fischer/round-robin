<?php

namespace App\Http\Requests;

use App\Models\GameLogic;
use Illuminate\Foundation\Http\FormRequest;

class GameCreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'logic' => ['required', 'in:' . implode(',', GameLogic::get()->toArray())],
        ];
    }
}
