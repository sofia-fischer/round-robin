<?php

namespace App\Http\Livewire;

use App\Models\Game;
use App\Queue\Events\GameEnded;
use App\Queue\Events\GameRoundAction;
use App\Queue\Events\PlayerKicked;
use App\Queue\Events\PlayerUpdated;
use Illuminate\Support\Str;
use Livewire\Component;

class WaveLengthComponent extends Component
{
    public Game $game;

    public int $value = 50;

    public ?string $clue = null;

    public function render()
    {
        $step = 'start';
        switch (true) {
            case !!($this->game->currentRound->completed_at):
                $step = 'completed';
                break;
            case !!($this->game->currentRound->payload['clue'] ?? false):
                $step = 'clue-given';
                break;
        }

        return view('livewire.WaveLengthComponent', [
            'step'     => $step,
            'antonym1' => Str::title(array_key_first($this->game->currentRound->payload['antonyms'])),
            'antonym2' => Str::title($this->game->currentRound->payload['antonyms'][array_key_first($this->game->currentRound->payload['antonyms'])]),
        ]);
    }

    /**
     * @return array
     */
    public function getListeners() : array
    {
        return [
            'echo:' . 'Game.' . $this->game->uuid . ',.' . GameEnded::class             => '$refresh',
            'echo:' . 'Game.' . $this->game->uuid . ',.' . GameRoundAction::class       => '$refresh',
            'echo:' . 'Group.' . $this->game->group->uuid . ',.' . PlayerUpdated::class => '$refresh',
            'echo:' . 'Group.' . $this->game->group->uuid . ',.' . PlayerKicked::class  => 'handlePlayerKick',
        ];
    }

    public function handlePlayerKick($playerKicked)
    {
        if ($playerKicked['player_id'] == $this->game->currentRound->active_player_id) {
            $this->nextRound();
        }
    }

    public function giveClue()
    {
        $this->game->roundAction(['clue' => $this->clue]);
    }

    public function setGuess()
    {
        $this->game->roundAction(['guess' => $this->value]);
    }

    public function nextRound()
    {
        $this->value = 50;
        $this->clue = null;

        $this->game->endRound();
    }
}
