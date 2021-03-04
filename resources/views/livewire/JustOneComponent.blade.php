<div class="w-full">
    <?php
    /* @var App\Models\Game $game */
    /* @var App\Models\Move $move */
    /* @var App\Models\Player $player */
    ?>

    <div class="flex justify-between max-w-xl mx-auto">
        <div class="text-sm {{ $step == 'start' ? 'text-purple-600' : 'text-gray-400' }}">
            All players give a clue
        </div>

        <div class="text-sm {{ $step == 'clue-given' ? 'text-purple-600' : 'text-gray-400' }}">
            {{ $game->currentRound->activePlayer->name }} guesses
        </div>

        <div class="text-sm {{ $step == 'completed' ? 'text-purple-600' : 'text-gray-400' }}">
            {{ $game->currentRound->activePlayer->name }} starts next round
        </div>
    </div>

    <div class="flex mb-8 m-4 text-center content-center font-semibold">
        @if($step == 'start')
            @if($game->authenticatedPlayerIsActive)
                <div class="mx-auto">
                    Wait for the clues of the other players...
                </div>
            @else
                <div>
                    The word {{ $game->currentRound->activePlayer->name }} must guess is
                    <div>
                        {{ $this->game->currentRound->payload['word'] }}
                    </div>
                </div>

                <div>
                    <input wire:model.defer='clue' wire:keydown.enter="giveClue"
                           class="flex-grow border-b-2 border-gray-700 max-w-xl mx-auto">
                    <label class="self-center mr-4">Give a one-worded clue</label>
                </div>
            @endif
        @elseif($step == 'clue-given')
            @if($game->authenticatedPlayerIsActive)
                <div>
                    <input wire:model.defer='value' wire:keydown.enter="giveGuess"
                           class="flex-grow border-b-2 border-gray-700 max-w-xl mx-auto">
                    <label class="self-center mr-4">What do you guess?</label>
                </div>
            @else
                <div>
                    The word {{ $game->currentRound->activePlayer->name }} must guess is
                    <div>
                        {{ $this->game->currentRound->payload['word'] }}
                    </div>
                </div>
            @endif

            <div class="flex flex-wrap justify-evenly ">
                @foreach($game->currentRound->moves()->where('player_id', '!=', $game->currentRound->active_player_id)->get() as $move)
                    <div class="rounded-xl text-center {{ 'bg-' . $move->player->activeColor ?? 'pink-500' }}">
                        <div>
                            {{ ($move->payload['visible'] ?? false) ? $move->payload['clue'] : '???' }}
                        </div>
                        <div class="pt-1 text-white px-2 flex-grow text-sm">
                            {{ $move->player->name }}
                        </div>
                    </div>
                @endforeach
            </div>
        @elseif($step == 'completed')
            <div>
                The word was
                <div>
                    {{ $this->game->currentRound->payload['word'] }}
                </div>
            </div>

            <div class="flex flex-wrap justify-evenly ">
                @foreach($game->currentRound->moves()->where('player_id', '!=', $game->currentRound->active_player_id)->get() as $move)
                    <div class="rounded-xl text-center {{ 'bg-' . $move->player->activeColor ?? 'pink-500' }}">
                        <div>
                            {{ $move->payload['clue'] ?? '???' }}
                        </div>
                        <div class="pt-1 text-white px-2 flex-grow text-sm">
                            {{ $move->player->name }}
                        </div>
                    </div>
                @endforeach
            </div>

            @if($game->authenticatedPlayerIsActive)
                <button wire:click="nextRound" class="bg-gray-700 text-white rounded-full my-4 mx-auto py-1 px-4">
                    Next round
                </button>
            @endif
        @endif
    </div>
</div>
