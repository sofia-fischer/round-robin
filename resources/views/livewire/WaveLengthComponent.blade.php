<div class="w-full">
    <?php
    /* @var App\Models\Game $game */
    /* @var App\Models\Move $move */
    /* @var App\Models\Player $player */
    ?>

    <div class="flex justify-between max-w-xl mx-auto">
        <div class="text-sm {{ $step == 'start' ? 'text-purple-600' : 'text-gray-400' }}">
            {{ $game->currentRound->activePlayer->name }} gives a clue
        </div>

        <div class="text-sm {{ $step == 'clue-given' ? 'text-purple-600' : 'text-gray-400' }}">
            All players give a guess
        </div>

        <div class="text-sm {{ $step == 'completed' ? 'text-purple-600' : 'text-gray-400' }}">
            {{ $game->currentRound->activePlayer->name }} starts next round
        </div>
    </div>

    <div class="flex mb-8 m-4 text-center content-center text-white font-semibold">
        <div class="py-1 px-4 bg-gray-700 h-8 flex-grow rounded-l-full">
            {{ $antonym1 }}
        </div>

        <div class="max-w-2xl w-full flex-grow">
            @if($step == 'completed' || $game->authenticatedPlayerIsActive)
                <div class="h-8 flex bg-red-400">
                    @if($game->authenticatedPlayerIsActive || $game->currentRound->completed_at)
                        <div class="bg-red-400"
                             style="width: {{ $game->currentRound->payload['waveLength'] - 16 }}%"></div>
                        <div class="bg-gradient-to-r from-red-400 to-orange-400 w-1/12"></div>
                        <div class="bg-gradient-to-r from-orange-400 to-yellow-300" style="width: 6%"></div>
                        <div class="bg-gradient-to-r from-yellow-300 via-white to-yellow-300" style="width: 4%"></div>
                        <div class="bg-gradient-to-r from-yellow-300 to-orange-400" style="width: 6%"></div>
                        <div class="bg-gradient-to-r from-orange-400 to-red-400 w-1/12"></div>
                    @endif
                </div>
                <div class="pt-7">
                    @foreach($game->currentRound->moves()->where('player_id', '!=', $game->currentRound->active_player_id)->get() as $move)
                        <div class="w-6 h-0  relative overflow-visible"
                             style="margin-left: {{ $move->payload['guess'] }}%">
                            <div
                                class="bg-{{ $move->player->activeColor ?? 'pink-500' }} w-4 hover:w-16 h-4 absolute bottom-2 right-2 rounded-b-lg rounded-r-lg hover-trigger">
                                <div
                                    class="absolute bg-{{ $move->player->activeColor ?? 'pink-500' }} border border-grey-100 px-4 hover-target rounded-b-lg rounded-r-lg">
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
                           class="w-full text-pink text-center"
                           {{ $game->authenticatedPlayerMove ? 'disabled' : '' }}
                           wire:model.defer="value">
                </div>
            @endif
        </div>

        <div class="py-1 px-4  bg-gray-700 h-8 flex-grow rounded-r-full">
            {{ $antonym2 }}
        </div>
    </div>

    <div class="flex flex-col content-center w-full text-center">
        @if($game->authenticatedPlayerIsActive)
            @if($step == 'start')
                <input wire:model.defer='clue' wire:keydown.enter="giveClue"
                       class="flex-grow border-b-2 border-gray-700 max-w-xl mx-auto">
                <label class="self-center mr-4">A word that fits conceptually the Spectrum value</label>

                <button wire:click="giveClue" class="bg-gray-700 text-white rounded-full my-4 mx-auto py-1 px-4">
                    Give Clue
                </button>
            @elseif($step == 'completed')
                <button wire:click="nextRound" class="bg-gray-700 text-white rounded-full my-4 mx-auto py-1 px-4">
                    Next round
                </button>
            @endif
        @else
            @if($step == 'clue-given' && !$game->authenticatedPlayerMove)
                <button wire:click="setGuess" class="bg-gray-700 text-white rounded-full my-4 mx-auto py-1 px-4">
                    Set Guess
                </button>
            @endif
        @endif

        @if($step != 'start')
            <div class="text-gray-500">
                The given clue is
                <h2 class="text-lg text-gray-700">
                    {{ $game->currentRound->payload['clue'] }}
                </h2>
            </div>
        @endif
    </div>

    <div class="flex flex-wrap w-full text-center px-4 pt-16">
        @foreach($game->players as $index => $player)
            <div class="flex flex-row justify-start m-2">
                <div class="flex overflow-hidden justify-between rounded-xl h-7 w-36
                        {{ 'bg-' . $player->activeColor ?? 'pink-500' }}">
                    <div class="pt-1 text-white px-2 flex-grow text-left">
                        {{ $player->name }}
                    </div>
                    <div class="text-white w-6 h-4">
                        @if($game->currentRound->moves->firstWhere('player_id', $player->id))
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        @endif
                    </div>
                    <div class="text-sm bg-white opacity-50 m-1 rounded-full px-2">
                        {{ $game ? $player->scoreInGame($game->id) : '' }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>
