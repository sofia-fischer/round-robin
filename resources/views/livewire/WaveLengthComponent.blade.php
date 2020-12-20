<div wire:poll.2000ms>
    <?php
    /* @var App\Models\Round $round */
    ?>
    <div class="text-center p-4 text-gray-500">
        <h2 class="text-lg text-orange-500 font-semibold pb-4">
            Wavelength - a social guessing game
        </h2>
        The active Player knows where the target is,
        but can only give a clue on the spectrum between two opposing concepts.
        After that, everyone has to guess where the target is.
    </div>

    <div class="flex mb-8 m-4 text-center content-center">
        <div class="p-1 bg-gray-100 h-8 flex-grow max-w-xs rounded-l-full">
            {{ Str::title(array_key_first($round->payload['antonyms'])) }}
        </div>

        <div class="max-w-2xl w-full flex-grow">
            @if($round->authenticatedPlayerIsActive || $round->authenticatedPlayerMove)
                <div class="h-8 flex bg-red-600">
                    @if($round->authenticatedPlayerIsActive || $round->completed_at)
                        <div class="bg-red-600" style="width: {{ $round->payload['waveLength'] - 16 }}%"></div>
                        <div class="bg-orange-400" style="width: 8%"></div>
                        <div class="bg-yellow-300" style="width: 6%"></div>
                        <div class="bg-green-500" style="width: 4%"></div>
                        <div class="bg-yellow-300" style="width: 6%"></div>
                        <div class="bg-orange-400" style="width: 8%"></div>
                    @endif
                </div>
                @foreach($round->moves()->where('player_id', '!=', $round->active_player_id)->get() as $move)
                    <div class="bg-red-600 mt-1 w-6 h-6 rounded-full relative"
                         style="margin-left: {{ $move->payload['guess'] }}%">
                        <div class="bg-red-600 w-4 h-4 absolute bottom-2 right-2"></div>
                    </div>
                @endforeach
            @else
                <div class="bg-gray-100 h-8 p-2">
                    <input type="range" min="1" max="100"
                           class="w-full text-orange"
                           wire:model.defer="value">
                </div>
            @endif
        </div>

        <div class="p-1 bg-gray-100 h-8 flex-grow max-w-xs rounded-r-full">
            {{ Str::title($round->payload['antonyms'][array_key_first($round->payload['antonyms'])]) }}
        </div>
    </div>

    <div class="flex flex-col content-center max-w-xl mx-auto text-center">
        @if($round->authenticatedPlayerIsActive && !($round->payload['clue'] ?? false))

            <input wire:model.defer='clue' class="flex-grow border-b-2 border-orange-500 ">
            <label class="self-center mr-4">A word that fits conceptually the Spectrum value</label>

            <button wire:click="giveClue" class="bg-orange-500 text-white rounded-full my-4 mx-auto py-1 px-4">
                Give Clue
            </button>
        @endif

        @if(!$round->authenticatedPlayerIsActive && !$round->authenticatedPlayerMove && ($round->payload['clue'] ?? false))
            <button wire:click="setGuess" class="bg-orange-500 text-white rounded-full my-4 mx-auto py-1 px-4">
                Set Guess
            </button>
        @endif

        @if($round->payload['clue'] ?? false)
            <div class="text-gray-500">
                The given clue is
                <h2 class="text-lg text-orange-500">
                    {{ $round->payload['clue'] }}
                </h2>
            </div>
        @else
            <div class="text-gray-500">
                Waiting for the clue of the active Player...
            </div>
        @endif

        @if($round->authenticatedPlayerIsActive && $round->completed_at)
            <button wire:click="nextRound" class="bg-orange-500 text-white rounded-full my-4 mx-auto py-1 px-4">
                Next round
            </button>
        @endif
    </div>
</div>
