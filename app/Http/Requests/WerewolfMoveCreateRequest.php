<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\WerewolfGame;
use Illuminate\Foundation\Http\FormRequest;

class WerewolfMoveCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var WerewolfGame $game */
        $game = $this->route('game');
        $board = $game->getCurrentWerewolfBoard();
        $player = $game->players()->where('user_id', $this->user()->id)->first();

        if (!$player) {
            return false;
        }

        return $board->canMakeSeeMove($player->id, $this->get('see'));
    }

    public function rules(): array
    {
        return [
            'see' => ['required'],
        ];
    }
}
