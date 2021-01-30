<div>
    <?php
    /* @var App\Models\Game $game */
    /* @var App\Models\Move $move */
    ?>

    <div class="text-center p-4 text-gray-500 text-sm flex">
        <div class="h-6 w-6 mr-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            The active Player knows where the target is,
            but can only give a clue on the spectrum between two opposing concepts.
            After that, everyone has to guess where the target is.
        </div>
    </div>

    <div class="flex mb-8 m-4 text-center content-center text-white font-semibold max-w-2xl mx-auto">
        <div class="py-1 px-4 bg-gray-700 h-8 flex-grow rounded-l-full">
            {{ Str::title(array_key_first($game->currentRound->payload['antonyms'])) }}
        </div>

        <div class="max-w-2xl w-full flex-grow">
            @if($game->currentRound->authenticatedPlayerIsActive || $game->currentRound->authenticatedPlayerMove)
                <div class="h-8 flex bg-red-400">
                    @if($game->currentRound->authenticatedPlayerIsActive || $game->currentRound->completed_at)
                        <div class="bg-red-400"
                             style="width: {{ $game->currentRound->payload['waveLength'] - 16 }}%"></div>
                        <div class="bg-gradient-to-r from-red-400 to-orange-400" style="width: 8%"></div>
                        <div class="bg-gradient-to-r from-orange-400 to-yellow-300" style="width: 6%"></div>
                        <div class="bg-gradient-to-r from-yellow-300 via-white to-yellow-300" style="width: 4%"></div>
                        <div class="bg-gradient-to-r from-yellow-300 to-orange-400" style="width: 6%"></div>
                        <div class="bg-gradient-to-r from-orange-400 to-red-400" style="width: 8%"></div>
                    @endif
                </div>
                <div class="pt-7">
                    @foreach($game->currentRound->moves()->where('player_id', '!=', $game->currentRound->active_player_id)->get() as $move)
                        <div class="w-6 h-0  relative overflow-visible"
                             style="margin-left: {{ $move->payload['guess'] }}%">
                            <div
                                class="bg-{{ $move->player->activeColor }} w-4 hover:w-16 h-4 absolute bottom-2 right-2 rounded-b-lg rounded-r-lg hover-trigger">
                                <div
                                    class="absolute bg-{{ $move->player->activeColor }} border border-grey-100 px-4 hover-target rounded-b-lg rounded-r-lg">
                                    {{ $move->player->name }}
                                </div>
                                <style>
                                    .hover-trigger .hover-target {
                                        display: none;
                                    }

                                    .hover-trigger:hover .hover-target {
                                        display: block;
                                    }
                                </style>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-gray-700  text-white font-semibold h-8 p-2">
                    <input type="range" min="1" max="100"
                           class="w-full text-pink"
                           wire:model.defer="value">
                </div>
            @endif
        </div>

        <div class="py-1 px-4  bg-gray-700 h-8 flex-grow rounded-r-full">
            {{ Str::title($game->currentRound->payload['antonyms'][array_key_first($game->currentRound->payload['antonyms'])]) }}
        </div>
    </div>

    <div class="flex flex-col content-center max-w-xl mx-auto text-center">
        @if($game->currentRound->authenticatedPlayerIsActive && !($game->currentRound->payload['clue'] ?? false))

            <input wire:model.defer='clue' class="flex-grow border-b-2 border-gray-700 ">
            <label class="self-center mr-4">A word that fits conceptually the Spectrum value</label>

            <button wire:click="giveClue" class="bg-gray-700 text-white rounded-full my-4 mx-auto py-1 px-4">
                Give Clue
            </button>
        @endif

        @if(($game->currentRound->payload['clue'] ?? false) && !$game->currentRound->authenticatedPlayerIsActive && !$game->currentRound->authenticatedPlayerMove && ($game->currentRound->payload['clue'] ?? false))
            <button wire:click="setGuess" class="bg-gray-700 text-white rounded-full my-4 mx-auto py-1 px-4">
                Set Guess
            </button>
        @endif

        @if(($game->currentRound->payload['clue'] ?? false))
            <div class="text-gray-500">
                The given clue is
                <h2 class="text-lg text-gray-700">
                    {{ $game->currentRound->payload['clue'] }}
                </h2>
            </div>
        @else
            <div class="text-gray-500">
                {{ $game->currentRound->authenticatedPlayerIsActive
                    ? 'You are the active Player. Give a hint that they will associate with the current goal'
                    : 'Waiting for the clue of the active Player...'}}
            </div>
        @endif

        @if($game->currentRound->authenticatedPlayerIsActive && $game->currentRound->completed_at)
            <button wire:click="nextRound" class="bg-gray-700 text-white rounded-full my-4 mx-auto py-1 px-4">
                Next round
            </button>
        @endif
    </div>
</div>
