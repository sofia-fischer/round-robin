<?php

namespace App\Http\Requests;

use App\Models\WerewolfGame;
use Illuminate\Foundation\Http\FormRequest;

class WerewolfMoveCreateRequest extends FormRequest
{
    public function rules(): array
    {
        /** @var WerewolfGame $game */
        $game      = $this->route('game');
        $playerIds = $game->players->pluck('id')->implode(',');
        $extraIds  = implode(',', [
            WerewolfGame::$leftAnonymRole,
            WerewolfGame::$centerAnonymRole,
            WerewolfGame::$rightAnonymRole
        ]);

        return match ($game->authenticatedRole) {
            WerewolfGame::WEREWOLF => [
                'see'  => ['required_without:vote', 'in:' . $extraIds],
                'vote' => ['required_without:see', 'in:' . $playerIds . ',nobody'],
            ],
            WerewolfGame::SEER => [
                'see'  => ['required_without:vote', 'in:' . $playerIds . ',' . $extraIds],
                'vote' => ['required_without:see', 'in:' . $playerIds . ',nobody'],
            ],
            WerewolfGame::ROBBER => [
                'steal' => ['required_without:vote', 'in:' . $playerIds],
                'vote'  => ['required_without:steal', 'in:' . $playerIds . ',nobody'],
            ],
            WerewolfGame::TROUBLEMAKER => [
                'switch1' => ['required_without_all:vote,switch2', 'in:' . $playerIds . ',' . $extraIds],
                'switch2' => ['required_without_all:switch1,vote', 'in:' . $playerIds . ',' . $extraIds],
                'vote'    => ['required_without_all:switch1,switch2', 'in:' . $playerIds . ',nobody'],
            ],
            WerewolfGame::DRUNK => [
                'drunk' => ['required_without:vote', 'in:' . $extraIds],
                'vote'  => ['required_without:drunk', 'in:' . $playerIds . ',nobody'],
            ],
            WerewolfGame::WATCHER => [],
            default => ['vote' => ['required', 'in:' . $playerIds . ',nobody']],
        };
    }

    public function payloadKey()
    {
        return match (true) {
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
