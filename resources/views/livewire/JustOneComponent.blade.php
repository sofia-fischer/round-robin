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
            {{ $game->currentPlayer->name }} guesses
        </div>

        <div class="text-sm {{ $game->isCompleted ? 'text-purple-600' : 'text-gray-400' }}">
            {{ $game->currentPlayer->name }} starts next round
        </div>
    </div>

    <form method="POST" action="{{ route('justone.move', ['game' => $game->id]) }}" class="mb-8 m-4 text-center pt-8">
        @csrf

        <div class="flex text-center justify-center">
            @if($game->isCompleted || !$game->authenticatedPlayerIsActive)
                <div class="bg-gray-300 rounded-full p-1 m-3 px-4 max-w-sm">
                    <div class="mx-4">{{ Str::upper($game->word) }}</div>
                    <div class="text-xs text-gray-500">The word to guess</div>
                </div>
            @endif
            @if($game->isCompleted)
                <div class="my-5">
                    {{ $game->authenticatedPlayerMove?->score ? '=' : '≠' }}
                </div>

                <div class="bg-gray-300 rounded-full p-1 m-3 px-4 max-w-sm">
                    <p class="mx-4">{{ Str::upper($game->guess) }}</p>
                    <p class="text-xs text-gray-500">The guessed word</p>
                </div>
            @endif
        </div>

        @if($game->isWaitingForClue && $game->authenticatedPlayerIsActive)
            <div class="mx-auto">
                Wait for the clues of the other players...
            </div>
        @endif

        @if($game->isWaitingForClue && !$game->authenticatedPlayerIsActive)
            <div class="flex justify-between">
                <div class="flex flex-col text-left mb-2">
                    <input id="clue" class="border-b-2 border-pink-500 bg-transparent" name="clue" autofocus/>
                    <label for="clue" class="text-pink-700 text-sm">
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
                        {{ ($game->isCompleted || $move->getPayloadWithKey('visible')) ? Str::upper($move->getPayloadWithKey('clue')) : '???' }}
                        <div class="text-xs">{{ $move->player->name }}</div>
                    </div>
                @endforeach
            </div>
        @endif

        @if($game->isWaitingForGuess && $game->authenticatedPlayerIsActive)
            <div class="flex justify-between">
                <div class="flex flex-col text-left mb-2">
                    <input id="guess" class="border-b-2 border-pink-500 bg-transparent" name="guess" autofocus/>
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
            <form action="{{ route('justone.round', ['game' => $game->id]) }}" method="POST" class="m-4">
                @csrf
                <button type="submit"
                    class="text-pink-700 bg-pink-200 py-2 px-4 m-2 font-semibold rounded-full hover:bg-red-400">
                    Next round
                </button>
            </form>
        </div>
    @endif
</div>
