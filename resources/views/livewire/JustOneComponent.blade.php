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

    <div class="mb-8 m-4 text-center pt-8">
        @if($step == 'start')
            @if($game->authenticatedPlayerIsActive)
                <div class="mx-auto">
                    Wait for the clues of the other players...
                </div>
            @else
                <div class="mx-auto">
                    <div class="flex flex-wrap justify-evenly">
                        <div class="bg-gray-300 rounded-full mx-auto p-1 m-3 px-4">
                            <div class="mx-4">
                                {{ Str::upper($game->currentRound->payload['word']) }}
                            </div>
                            <div class="text-xs text-gray-500">
                                The word to guess
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col text-center">
                        <input wire:model='clue' wire:keydown.enter="giveClue"
                               class="flex-grow border-b-2 border-purple-600 max-w-xl mx-auto text-center">
                        <label class="self-center mr-4 text-xs">Give a one-worded clue</label>
                    </div>

                    <button wire:click="giveClue" class="px-4 py-1 rounded-full text-sm bg-gradient-to-r from-purple-800 to-pink-700 text-white mt-8">
                        Give clue
                    </button>
                </div>
            @endif
        @elseif($step == 'clue-given')
            <div class="flex flex-wrap justify-evenly m-4 text-center text-white">
                @foreach($game->currentRound->moves()->where('player_id', '!=', $game->currentRound->active_player_id)->get() as $move)
                    <div class="rounded-full text-center px-4 m-2 {{ 'bg-' . $move->player->activeColor ?? 'pink-500' }}">
                        {{ ($move->payload['visible'] ?? false) ? Str::upper($move->payload['clue']) : '???' }}
                        <div class="text-xs">
                            {{ $move->player->name }}
                        </div>
                    </div>
                @endforeach
            </div>

            @if($game->authenticatedPlayerIsActive)
                <div class="flex flex-col text-center">
                    <input wire:model='value' wire:keydown.enter="giveGuess"
                           class="flex-grow border-b-2 border-purple-600 max-w-xl mx-auto text-center">
                    <label class="self-center mr-4 text-xs">What do you guess?</label>
                </div>

                <button wire:click="giveGuess" class="px-4 py-1 rounded-full text-sm bg-gradient-to-r from-purple-800 to-pink-700 text-white mt-8">
                    Guess
                </button>
            @else
                <div>
                    The word {{ $game->currentRound->activePlayer->name }} must guess is
                    <div class="my-8">
                        {{ Str::upper($game->currentRound->payload['word']) }}
                    </div>
                </div>
            @endif
        @elseif($step == 'completed')
            <div class="flex flex-wrap justify-evenly">
                <div class="bg-gray-300 rounded-full mx-auto p-1 m-3 px-2">
                    <div class="mx-4">
                        {{ Str::upper($game->currentRound->payload['word']) }}
                    </div>
                    <div class="text-xs text-gray-500">
                        The word to guess
                    </div>
                </div>

                <div class="bg-gray-300 rounded-full mx-auto p-1 m-3 px-2 flex items-center">
                    <div>
                        <div>
                            {{ Str::upper($game->currentRound->moves()->where('player_id', $game->currentRound->active_player_id)->first()->payload['guess'] ?? '') }}
                        </div>
                        <div class="text-xs text-gray-500">
                            {{ $game->currentRound->activePlayer->name }}'s guess
                        </div>
                    </div>
                    <div class="text-white w-8 h-8  rounded-full p-1 {{ ($game->authenticatedPlayerMove->score ?? false) ? 'bg-green-700' : 'bg-red-700' }}">
                        @if($game->authenticatedPlayerMove->score ?? false)
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                            </svg>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap justify-evenly m-4 text-center text-white">
                @foreach($game->currentRound->moves()->where('player_id', '!=', $game->currentRound->active_player_id)->get() as $move)
                    <div class="rounded-full text-center px-4 m-2 {{ 'bg-' . $move->player->activeColor ?? 'pink-500' }}">
                        {{ ($move->payload['visible'] ?? false) ? Str::upper($move->payload['clue']) : '???' }}
                        <div class="text-xs">
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
