<?php
/* @var App\Models\Game | null $game */

/* @var App\Models\Player $player */
?>

<x-app-layout>
    <div class="max-w-2xl mx-auto mt-4 sm:px-6 lg:px-8 ">
        <a href="{{ route("{$game->logic_identifier}.show", ['game' => $game->id]) }}">
            <div class="rounded-xl bg-pink-600 my-2 p-2 hover:bg-purple-500 text-white text-center w-full flex justify-between">
                <x-icons.arrow-left class="h-6"/>
                <div> Back to the game</div>
                <x-icons.arrow-left class="h-6"/>
            </div>
        </a>
        @if($game)
            <div class="rounded-xl bg-gray-800 my-2 p-4">
                <h1 class="text-xl text-center font-semibold text-white mb-4">Game Settings</h1>
                <p class="text-gray-200 text-center my-1 w-full">
                    {{ $game->token }}<strong> Code to join</strong>
                    <br>
                    {{ $game->rounds->count() }}<strong> Rounds</strong>
                    <br>
                    {{ $game->moves()->where('user_id', \Illuminate\Support\Facades\Auth::id())->where('score', '>', 0)->count() }}
                    <strong> Rounds with success</strong>
                    <br>
                    {{ $game->started_at?->toDateString() ?? 'Not' }}<strong> Started</strong>
                    <br>
                    {{ $game->hostPlayer->name }}<strong> is Host</strong>
                </p>
            </div>

            @if($game->host_user_id === \Illuminate\Support\Facades\Auth::id())
                <div class="rounded-xl bg-gray-800 my-2 p-4">
                    <h1 class="text-xl text-center font-semibold text-white mb-4">Kick Player</h1>

                    <div class="my-1 w-full flex justify-center">
                        @foreach($game->players as $player)
                            <form method="POST" action="{{ route('player.destroy', ['player' => $player->id]) }}"
                                onsubmit="return confirm('The selected player will leave the game');">
                                @csrf
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="text-center text-white rounded-xl h-7 w-36 m-1 hover:text-black
                            {{ 'hover:bg-' . $player->color()->passiveColor()}} {{ $player->color()->background()}}">
                                    {{ $player->user_id === \Illuminate\Support\Facades\Auth::id() ? 'Myself' : $player->name }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-xl bg-gray-800 my-2 p-4">
                    <h1 class="text-xl text-center font-semibold text-white mb-4">Game Settings</h1>

                    <form class="my-1 w-full flex justify-center mb-4" method="POST"
                        onsubmit="return confirm('Just skip to the next round');"
                        action="{{ route('game.round', ['game' => $game->id]) }}">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="game_id" value="{{$game?->id}}">
                        <button type="submit" class="text-center rounded-xl h-7 px-2 bg-pink-600 text-white hover:bg-purple-500">
                            🐛 Enforce Next Round
                        </button>
                    </form>

                    <form class="my-1 w-full flex justify-center" method="POST"
                        onsubmit="return confirm('Do you want to destroy the current game?');"
                        action="{{ route('game.destroy', ['game' => $game->id]) }}">
                        @csrf
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="text-center rounded-xl h-7 px-2 bg-red-700 text-white hover:bg-purple-500">
                            ⚠️ DESTROY GAME
                        </button>
                    </form>
                </div>
            @else
                <div class="rounded-xl bg-gray-800 my-2 p-4">
                    <h1 class="text-xl text-center font-semibold text-white mb-4">Leave Game</h1>

                    <form class="my-1 w-full flex justify-center" method="POST" action="{{ route('player.destroy', ['player' => $game->authenticatedPlayer->id]) }}"
                        onsubmit="return confirm('The selected player will leave the game');">
                        @csrf
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="text-center text-white rounded-xl h-7 w-36 m-1 hover:text-black
                            {{ 'hover:bg-' . $game->authenticatedPlayer->color()->passiveColor()}} {{ $game->authenticatedPlayer->color()->background() }}">
                            {{ $game->authenticatedPlayer->name }} wants to go?
                        </button>
                    </form>
                </div>
            @endif
        @endif
    </div>
</x-app-layout>
