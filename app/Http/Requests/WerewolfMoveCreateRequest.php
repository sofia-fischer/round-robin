<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WerewolfMoveCreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'seeAnonymous' => ['nullable', 'in:one,two,three'],
            'see'          => ['nullable', 'exists:player,id'],
            'steal'        => ['nullable', 'exists:player,id'],
            'switch1'      => ['nullable'],
            'switch2'      => ['nullable'],
            'drunk'        => ['nullable', 'in:one,two,three'],
            'vote'         => ['nullable'],
        ];
    }

    public function payloadKey()
    {
        return match (true) {
            (bool) $this->input('seeAnonymous') => 'seeAnonymous',
            (bool) $this->input('see') => 'see',
            (bool) $this->input('steal') => 'steal',
            (bool) $this->input('switch1') => 'switch1',
            (bool) $this->input('switch2') => 'switch2',
            (bool) $this->input('drunk') => 'drunk',
            (bool) $this->input('vote') => 'vote',
        };
    }

    public function payloadValue()
    {
        return is_numeric($this->input($this->payloadKey()))
            ? (int) $this->input($this->payloadKey())
            : $this->input($this->payloadKey());
    }
}
