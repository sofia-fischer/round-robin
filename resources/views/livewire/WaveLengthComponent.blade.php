<div class="w-full">
    <?php
    /* @var App\Models\WaveLengthGame $game */
    /* @var App\Models\Move $move */
    ?>

    <div class="flex justify-between max-w-xl mx-auto">
        <div class="text-sm {{ $game->isWaitingForClue ? 'text-purple-600' : 'text-gray-400' }}">
            {{ $game->currentRound->activePlayer->user->name }} gives a clue
        </div>

        <div class="text-sm {{ $game->isWaitingForGuess ? 'text-purple-600' : 'text-gray-400' }}">
            All players give a guess
        </div>

        <div class="text-sm {{ $game->isCompleted ? 'text-purple-600' : 'text-gray-400' }}">
            {{ $game->currentRound->activePlayer->user->name }} starts next round
        </div>
    </div>

    <form method="POST" action="{{ route('wavelength.move', ['game' => $game->uuid]) }}" class="m-4">
        @csrf

        {{-- Indicator --}}
        <div class="flex mb-8 text-center content-center text-white font-semibold">
            <div class="py-1 px-4 bg-gray-700 h-8 flex-grow rounded-l-full">{{ $game->currentPayloadAttribute('antonym1') }}</div>

            <div class="max-w-2xl w-full flex-grow">
                @if($game->isCompleted || $game->authenticatedPlayerIsActive)
                    {{-- Inactive Indicator --}}
                    <div class="h-8 flex bg-red-400">
                        <div class="bg-red-400"
                            style="width: {{ $game->currentPayloadAttribute('waveLength', 16) - 16 }}%"></div>
                        <div class="bg-gradient-to-r from-red-400 to-orange-400 w-1/12"></div>
                        <div class="bg-gradient-to-r from-orange-400 to-yellow-300" style="width: 6%"></div>
                        <div class="bg-gradient-to-r from-yellow-300 via-white to-yellow-300" style="width: 4%"></div>
                        <div class="bg-gradient-to-r from-yellow-300 to-orange-400" style="width: 6%"></div>
                        <div class="bg-gradient-to-r from-orange-400 to-red-400 w-1/12"></div>
                    </div>
                    <div class="pt-7">
                        @foreach($moves as $move)
                            <div class="w-6 h-0  relative overflow-visible"
                                style="margin-left: {{ $move->payloadAttribute('guess') }}%">
                                <div class="bg-{{ $move->player->activeColor ?? 'pink-500' }}
                                    w-4 hover:w-16 h-4 absolute bottom-2 right-2
                                    rounded-b-lg rounded-r-lg hover-trigger">
                                    <div class="absolute bg-{{ $move->player->activeColor ?? 'pink-500' }}
                                        border border-grey-100 px-4 hover-target rounded-b-lg rounded-r-lg">
                                        {{ $move->player->user->name }}
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
                    {{-- Active Indicator --}}
                    <div class="bg-gray-700  text-white font-semibold h-8 p-2">
                        <input id="guess" name="guess" :value="old('guess')" type="range" min="1" max="100" class="w-full text-pink text-center"
                            {{ $game->authenticatedPlayerIsActive ? 'disabled' : '' }}>
                    </div>
                @endif
            </div>

            <div class="py-1 px-4  bg-gray-700 h-8 flex-grow rounded-r-full">{{ $game->currentPayloadAttribute('antonym2') }}</div>
        </div>

        {{-- Post Move --}}
        <div class="flex flex-col content-center w-full text-center">
            {{-- Set Clue --}}
            @if($game->isWaitingForClue && $game->authenticatedPlayerIsActive)
                <div class="flex justify-between">
                    <div class="flex flex-col text-left mb-2">
                        <input id="clue"
                            class="border-b-2 border-pink-500 bg-transparent"
                            name="clue"
                            :value="old('clue')"
                            autofocus
                        />
                        <label for="clue" class="text-pink-700 text-sm">
                            One word that fits conceptually the Spectrum value
                        </label>
                        @error('clue')<p class="input-error">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit"
                        class="text-pink-700 bg-pink-200 py-2 px-4 m-2 font-semibold rounded-full hover:bg-red-400">
                        Give Clue
                    </button>
                </div>
            @endif

            @if(! $game->isWaitingForClue)
                <div class="text-gray-500">
                    The given clue is
                    <h2 class="text-lg text-gray-700">{{ $game->currentPayloadAttribute('clue') }}</h2>
                </div>
            @endif

            @if($game->isWaitingForGuess && !$game->authenticatedPlayerIsActive)
                <button type="submit" class="text-pink-700 bg-pink-200 py-2 px-4 m-2 mx-auto font-semibold rounded-full hover:bg-red-400">
                    Set Guess
                </button>
            @endif
        </div>
    </form>

    {{-- End Round --}}
    @if($game->isCompleted && $game->authenticatedPlayerIsActive)
        <div>
            <form action="{{ route('wavelength.round', ['game' => $game->uuid]) }}" method="POST" class="m-4">
                @csrf
                <button type="submit"
                    class="text-pink-700 bg-pink-200 py-2 px-4 m-2 font-semibold rounded-full hover:bg-red-400">
                    Next round
                </button>
            </form>
        </div>
    @endif
</div>
