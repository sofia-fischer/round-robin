<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JustOneMoveCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var \App\Models\JustOneGame $game */
        $game = $this->route('game');

        if (! $game->authenticatedPlayerIsActive && ! $this->clue()) {
            return false;
        }

        if ($game->authenticatedPlayerIsActive && ! $this->guess()) {
            return false;
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'clue' => ['required_without:guess', 'string', 'alpha_dash'],
            'guess' => ['required_without:clue', 'string'],
        ];
    }

    public function guess()
    {
        return $this->input('guess');
    }

    public function clue()
    {
        return $this->input('clue');
    }
}
