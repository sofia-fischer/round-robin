<div class="w-full">
    <?php
    /* @var App\Models\JustOneGame $game */
    /* @var App\Models\Move $move */
    /* @var App\Models\Player $player */
    ?>

    <div class="flex justify-between max-w-xl mx-auto">
        <div class="text-sm {{ $game->isWaitingForClue ? 'text-purple-600' : 'text-gray-400' }}">
            All players give a clue
        </div>

        <div class="text-sm {{ $game->isWaitingForGuess ? 'text-purple-600' : 'text-gray-400' }}">
            {{ $game->currentPlayer->user->name }} guesses
        </div>

        <div class="text-sm {{ $game->isCompleted ? 'text-purple-600' : 'text-gray-400' }}">
            {{ $game->currentPlayer->user->name }} starts next round
        </div>
    </div>

    <form method="POST" action="{{ route('justone.move', ['game' => $game->uuid]) }}" class="mb-8 m-4 text-center pt-8">
        @csrf

        <div class="flex justify-center text-center">
            @if($game->isCompleted || !$game->authenticatedPlayerIsActive)
                <div class="my-2">
                    <p>{{ Str::upper($game->word) }}</p>
                    <p>The word to guess</p>
                </div>
            @endif
            @if($game->isCompleted)
                <div class="my-2">
                    {{ $game->authenticatedPlayerMove?->score ? '=' : '≠' }}
                </div>

                <div class="my-2">
                    <p>{{ Str::upper($game->guess) }}</p>
                    <p>The guessed word</p>
                </div>
            @endif
        </div>

        @if($game->isCompleted || !$game->authenticatedPlayerIsActive)
            <div class="bg-gray-300 rounded-full mx-auto p-1 m-3 px-4 max-w-sm">
                <div class="mx-4">{{ Str::upper($game->word) }}</div>
                <div class="text-xs text-gray-500">The word to guess</div>
            </div>
        @endif

        @if($game->isWaitingForClue && $game->authenticatedPlayerIsActive)
            <div class="mx-auto">
                Wait for the clues of the other players...
            </div>
        @endif

        @if($game->isWaitingForClue && !$game->authenticatedPlayerIsActive)
            <div class="flex justify-between">
                <div class="flex flex-col text-left mb-2">
                    <input id="clue"
                        class="border-b-2 border-pink-500 bg-transparent"
                        name="clue"
                        :value="old('clue')"
                        autofocus
                    />
                    <label for="guess" class="text-pink-700 text-sm">
                        One word to help the active Player to guess the word
                    </label>
                    @error('clue')<p class="input-error">{{ $message }}</p>@enderror
                </div>

                <button type="submit"
                    class="text-pink-700 bg-pink-200 py-2 px-4 m-2 font-semibold rounded-full hover:bg-red-400">
                    Give Clue
                </button>
            </div>
        @endif

        @if($game->isWaitingForGuess || $game->isCompleted)
            <div class="flex flex-wrap justify-evenly m-4 text-center text-white">
                @foreach($game->currentRound->moves()->where('player_id', '!=', $game->currentRound->active_player_id)->get() as $move)
                    <div class="rounded-full text-center px-4 m-2 {{ 'bg-' . $move->player->activeColor }}">
                        {{ ($game->isCompleted || $move->payloadAttribute('visible')) ? Str::upper($move->payloadAttribute('clue')) : '???' }}
                        <div class="text-xs">{{ $move->player->user->name }}</div>
                    </div>
                @endforeach
            </div>
        @endif

        @if($game->isWaitingForGuess && $game->authenticatedPlayerIsActive)
            <div class="flex justify-between">
                <div class="flex flex-col text-left mb-2">
                    <input id="guess"
                        class="border-b-2 border-pink-500 bg-transparent"
                        name="guess"
                        :value="old('guess')"
                        autofocus
                    />
                    <label for="guess" class="text-pink-700 text-sm">
                        Your guess of the word
                    </label>
                    @error('guess')<p class="input-error">{{ $message }}</p>@enderror
                </div>

                <button type="submit"
                    class="text-pink-700 bg-pink-200 py-2 px-4 m-2 font-semibold rounded-full hover:bg-red-400">
                    Guess
                </button>
            </div>
        @endif
    </form>


    {{-- End Round --}}
    @if($game->isCompleted && $game->authenticatedPlayerIsActive)
        <div>
            <form action="{{ route('justone.round', ['game' => $game->uuid]) }}" method="POST" class="m-4">
                @csrf
                <button type="submit"
                    class="text-pink-700 bg-pink-200 py-2 px-4 m-2 font-semibold rounded-full hover:bg-red-400">
                    Next round
                </button>
            </form>
        </div>
    @endif
</div>
