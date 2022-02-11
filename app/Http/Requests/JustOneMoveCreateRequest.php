<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JustOneMoveCreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'clue'  => ['required_without:guess', 'string', 'alpha_dash'],
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
