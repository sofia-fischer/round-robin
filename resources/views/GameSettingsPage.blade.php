<?php
/* @var App\Models\Game $game */

/* @var App\Models\Player $player */
?>

<x-app-layout>
    <div class="max-w-2xl mx-auto mt-4 sm:px-6 lg:px-8 ">

        @if($game)
            <div class="rounded-xl bg-gray-800 my-2 p-4">
                <h1 class="text-xl text-center font-semibold text-white mb-4">Stats</h1>

                <p class="text-gray-200 text-center my-1 w-full">
                    {{ $game->rounds->count() }}<b> Rounds</b>
                    <br>
                    {{ $game->moves()->where('user_id', \Illuminate\Support\Facades\Auth::id())->where('score', '>', 0)->count() }}
                    <b> Rounds with success</b>
                    <br>
                    {{ $game->started_at->toDateString() }}<b> Started</b>
                    <br>
                    {{ $game->hostPlayer->user->name }}<b> is Host</b>
                </p>
            </div>

            @if($game->host_user_id === \Illuminate\Support\Facades\Auth::id())
                <div class="rounded-xl bg-gray-800 my-2 p-4">
                    <h1 class="text-xl text-center font-semibold text-white mb-4">Kick Player</h1>

                    <div class="my-1 w-full flex justify-center">
                        @foreach($game->players as $player)
                            <form method="POST" action="{{ route('player.destroy', ['player' => $player->uuid]) }}"
                                onsubmit="return confirm('The selected player will leave the game');">
                                @csrf
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="text-center text-white rounded-xl h-7 w-36 m-1 hover:text-black
                            {{ 'hover:bg-' . $player->passiveColor}} {{ 'bg-' . $player->activeColor}}">
                                    {{ $player->user_id === \Illuminate\Support\Facades\Auth::id() ? 'Myself' : $player->user->name }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-xl bg-gray-800 my-2 p-4">
                    <h1 class="text-xl text-center font-semibold text-white mb-4">Game Settings</h1>

                    <form class="my-1 w-full flex justify-center mb-4" method="POST"
                        onsubmit="return confirm('Just skip to the next round');"
                        action="{{ route('game.round', ['game' => $game->uuid]) }}">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="game_id" value="{{$game?->id}}">
                        <button type="submit" class="text-center rounded-xl h-7 px-2 bg-pink-600 text-white hover:bg-purple-500">
                            🐛 Enforce Next Round
                        </button>
                    </form>

                    <form class="my-1 w-full flex justify-center" method="POST"
                        onsubmit="return confirm('Do you want to destroy the current game?');"
                        action="{{ route('game.destroy', ['game' => $game->uuid]) }}">
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

                    <form class="my-1 w-full flex justify-center" method="POST" action="{{ route('player.destroy', ['player' => $game->authenticatedPlayer->uuid]) }}"
                        onsubmit="return confirm('The selected player will leave the game');">
                        @csrf
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="text-center text-white rounded-xl h-7 w-36 m-1 hover:text-black
                            {{ 'hover:bg-' . $game->authenticatedPlayer->passiveColor}} {{ 'bg-' . $game->authenticatedPlayer->activeColor}}">
                           {{ $game->authenticatedPlayer->user->name }} wants to go?
                        </button>
                    </form>
                </div>
            @endif
        @endif

        <div class="rounded-xl bg-gray-800 my-2 p-4">
            <h1 class="text-xl text-center font-semibold text-white mb-4">Account Settings</h1>
            <form class="flex flex-col my-2" method="POST" action="{{ route('user.update', ['user' => $game->authenticatedPlayer->user, 'game' => $game]) }}">
                @csrf
                <div>
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="game_id" value="{{$game?->id}}">
                    <select name="color" id="color" onchange="this.form.submit()" class="px-4 rounded-xl text-white {{ 'bg-' . $game->authenticatedPlayer->activeColor }}">
                        <option value="" selected disabled hidden> {{ $game->authenticatedPlayer->user->color ?? 'pink' }}</option>
                        <option value="gray" class="bg-gray-500">gray</option>
                        <option value="red" class="bg-red-500">red</option>
                        <option value="orange" class="bg-orange-500">orange</option>
                        <option value="yellow" class="bg-yellow-500">yellow</option>
                        <option value="green" class="bg-green-500">green</option>
                        <option value="teal" class="bg-teal-500">teal</option>
                        <option value="blue" class="bg-blue-500">blue</option>
                        <option value="purple" class="bg-purple-500">purple</option>
                        <option value="pink" class="bg-pink-500">pink</option>
                    </select>
                </div>
                <label for="color" class="text-white text-sm">Color</label>
            </form>
            <form method="POST" class="w-full flex justify-between my-2" action="{{ route('user.update', ['user' => $game->authenticatedPlayer->user]) }}">
                @csrf
                <div class="flex flex-col text-left mb-2">
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="game_id" value="{{$game?->id}}">
                    <input id="name"
                        class="border-b-2 border-white bg-transparent text-white"
                        placeholder="{{ $game->authenticatedPlayer->user->name }}"
                        name="name"
                    />
                    <label for="name" class="text-white text-sm">Name</label>
                    @error('name')<p class="input-error">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="bg-pink-700 text-white rounded-full hover:bg-purple-500 my-3 px-4">
                    Rename
                </button>
            </form>

            <form method="POST" class="w-full flex justify-between my-2" action="{{ route('user.update', ['user' => $game->authenticatedPlayer->user]) }}">
                @csrf
                <div class="flex flex-col text-left mb-2">
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="game_id" value="{{$game?->id}}">
                    <input id="email"
                        type="email"
                        class="border-b-2 border-white bg-transparent text-white"
                        placeholder="{{ $game->authenticatedPlayer->user->email ??'Set up an Email' }}"
                        name="email"
                    />
                    <label for="email" class="text-white text-sm">Email</label>
                    @error('email')<p class="input-error">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="bg-pink-700 text-white rounded-full hover:bg-purple-500 my-3 px-4">
                    Save Email
                </button>
            </form>

            <form method="POST" class="w-full flex justify-between my-2"
                action="{{ route('user.update', ['user' => $game->authenticatedPlayer->user]) }}">
                @csrf
                <div class="flex flex-col text-left mb-2">
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="game_id" value="{{$game?->id}}">
                    <input id="password"
                        type="password"
                        class="border-b-2 border-white bg-transparent text-white"
                        placeholder="{{ '*******' }}"
                        name="password"
                    />
                    <label for="password" class="text-white text-sm">Password</label>
                    @error('password')<p class="input-error">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="bg-pink-700 text-white rounded-full hover:bg-purple-500 my-3 px-4">
                    Save Password
                </button>
            </form>
        </div>

        @if($game->authenticatedPlayer->user->email)
            <div class="rounded-xl bg-gray-800 my-2 p-4">
                <h1 class="text-xl text-center font-semibold text-white mb-4">Destroy Account</h1>

                <form class="my-1 w-full flex justify-center" method="POST"
                    onsubmit="return confirm('😢 Sorry to see you go... ');"
                    action="{{ route('user.delete',  ['user' => $game->authenticatedPlayer->user]) }}">
                    @csrf
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="text-center rounded-xl h-7 px-2 bg-pink-600 text-white hover:bg-purple-500">
                        🚨 Delete Account
                    </button>
                </form>
            </div>
        @endif
    </div>
</x-app-layout>
