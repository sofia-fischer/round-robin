<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\WaveLengthGame;
use App\Queue\Events\GameEnded;
use App\Queue\Events\PlayerUpdated;
use App\Queue\Events\PlayerDestroyed;
use App\Queue\Events\GameRoundAction;

class WaveLengthComponent extends Component
{
    public WaveLengthGame $game;

    public int $value = 50;

    public ?string $clue = null;

    public function render()
    {
        if (! $this->game->started_at) {
            $this->game->start();
        }

        $step = 'start';
        switch (true) {
            case ! ! ($this->game->currentRound->completed_at ?? false):
                $step = 'completed';
                break;
            case ! ! ($this->game->currentRound->payload['clue'] ?? false):
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
    public function getListeners(): array
    {
        return [
            'echo:' . 'Game.' . $this->game->uuid . ',.' . GameRoundAction::class => '$refresh',
            'echo:' . 'Game.' . $this->game->uuid . ',.' . GameEnded::class       => '$refresh',
            'echo:' . 'Game.' . $this->game->uuid . ',.' . PlayerUpdated::class   => '$refresh',
            'echo:' . 'Game.' . $this->game->uuid . ',.' . PlayerDestroyed::class => 'nextRound',
        ];
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

        $this->game->start();
    }
}
