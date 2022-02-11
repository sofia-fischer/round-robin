<?php
/* @var App\Models\Game $game */

/* @var App\Models\Player $player */
?>

<x-app-layout>
    <div class="max-w-2xl mx-auto mt-4 sm:px-6 lg:px-8 ">

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

        <div class="rounded-xl bg-gray-800 my-2 p-4">
            <h1 class="text-xl text-center font-semibold text-white mb-4">Kick Player</h1>

            <div class="my-1 w-full flex justify-center">
                @foreach($game->players as $player)
                    <form method="DELETE" action="{{ route('player.destroy', ['player' => $player->uuid]) }}">
                        @csrf
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

            @if($game->logic_identifier === \App\Models\WaveLengthGame::$logic_identifier)
                <form class="my-1 w-full flex justify-center" method="DELETE" action="{{ route('wavelength.round', ['game' => $game->uuid]) }}">
                    @csrf
                    <button type="submit" class="text-center rounded-xl h-7 px-2 bg-pink-600 text-white hover:bg-purple-500">
                        ⚠️ Enforce Next Round
                    </button>
                </form>
            @endif
            <form class="my-1 w-full flex justify-center" method="DELETE" action="{{ route('game.destroy', ['game' => $game->uuid]) }}">
                @csrf
                <button type="submit" class="text-center rounded-xl h-7 px-2 bg-red-700 text-white hover:bg-purple-500">
                    ⚠️ DESTROY GAME
                </button>
            </form>
        </div>

        <div class="rounded-xl bg-gray-800 my-2 p-4">
            <h1 class="text-xl text-center font-semibold text-white mb-4">Player Settings</h1>
            <form class="flex flex-col my-2" method="PUT" action="{{ route('player.update', ['player' => $player->uuid]) }}">
                <div>
                    <select name="color" id="color" onchange="this.form.submit()" class="px-4 rounded-xl text-white {{ 'bg-' . $player->activeColor }}">
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
            <form method="PUT" class="w-full flex justify-between my-2" action="{{ route('player.update', ['player' => $player->uuid]) }}">
                @csrf

                <div class="flex flex-col text-left mb-2">
                    <input id="name"
                        class="border-b-2 border-white bg-transparent"
                        placeholder="{{ $player->user->name }}"
                        name="name"
                    />
                    <label for="name" class="text-white text-sm">Name</label>
                    @error('name')<p class="input-error">{{ $message }}</p>@enderror
                </div>

                <button type="submit" class="bg-pink-700 text-white rounded-full hover:bg-purple-500 my-3 px-4">
                    Rename
                </button>
            </form>
        </div>

        <div class="rounded-xl bg-gray-800 my-2 p-4">
            <h1 class="text-xl text-center font-semibold text-white mb-4">Account Settings</h1>

            <div class="bg-orange-500 text-white rounded-xl w-full md:w-1/4 my-1">
                ✉️️ Change Email
            </div>
            <div class="bg-orange-200 rounded-xl flex-grow my-1 w-full flex flex-wrap">
                {{--                <form method="PUT" class="w-full flex justify-between" action="{{ route('player.update', ['player' => $player->uuid]) }}">--}}
                {{--                    @csrf--}}

                {{--                    <div class="flex flex-col text-left mb-2">--}}
                {{--                        <input id="name"--}}
                {{--                            class="border-b-2 border-white bg-transparent"--}}
                {{--                            name="name"--}}
                {{--                        />--}}
                {{--                        <label for="name" class="text-white text-sm">Name</label>--}}
                {{--                        @error('name')<p class="input-error">{{ $message }}</p>@enderror--}}
                {{--                    </div>--}}

                {{--                    <div>--}}
                {{--                        <button type="submit"--}}
                {{--                            class="bg-orange-700 text-white rounded-full hover:bg-purple-400 py-2 px-4">--}}
                {{--                            Rename--}}
                {{--                        </button>--}}
                {{--                    </div>--}}
                {{--                </form>--}}
            </div>
            <div class="bg-orange-500 text-white rounded-xl w-full md:w-1/4 my-1">
                🔐 Change Password
            </div>
            <div class="bg-orange-200 rounded-xl flex-grow my-1 w-full flex flex-wrap">
                {{--                <form method="PUT" class="w-full flex justify-between" action="{{ route('player.update', ['player' => $player->uuid]) }}">--}}
                {{--                    @csrf--}}

                {{--                    <div class="flex flex-col text-left mb-2">--}}
                {{--                        <input id="name"--}}
                {{--                            class="border-b-2 border-white bg-transparent"--}}
                {{--                            name="name"--}}
                {{--                        />--}}
                {{--                        <label for="name" class="text-white text-sm">Name</label>--}}
                {{--                        @error('name')<p class="input-error">{{ $message }}</p>@enderror--}}
                {{--                    </div>--}}

                {{--                    <div>--}}
                {{--                        <button type="submit"--}}
                {{--                            class="bg-orange-600 text-white rounded-full hover:bg-prange-400 py-2 px-4">--}}
                {{--                            Rename--}}
                {{--                        </button>--}}
                {{--                    </div>--}}
                {{--                </form>--}}
            </div>
            <div class="bg-orange-500 text-white rounded-xl w-full md:w-1/4 my-1">
                🚨 Delete Account
            </div>
            <div class="bg-orange-200 rounded-xl flex-grow my-1 w-full flex flex-wrap">
                {{--                <form method="DELETE" class="w-full flex justify-between" action="{{ route('auth.update', ['player' => $player->uuid]) }}">--}}
                {{--                    @csrf--}}

                {{--                    <div class="flex flex-col text-left mb-2">--}}
                {{--                        <input id="name"--}}
                {{--                            class="border-b-2 border-white bg-transparent"--}}
                {{--                            name="name"--}}
                {{--                        />--}}
                {{--                        <label for="name" class="text-white text-sm">Name</label>--}}
                {{--                        @error('name')<p class="input-error">{{ $message }}</p>@enderror--}}
                {{--                    </div>--}}

                {{--                    <div>--}}
                {{--                        <button type="submit"--}}
                {{--                            class="bg-orange-600 text-white rounded-full hover:bg-prange-400 py-2 px-4">--}}
                {{--                            Rename--}}
                {{--                        </button>--}}
                {{--                    </div>--}}
                {{--                </form>--}}
            </div>
        </div>
    </div>
</x-app-layout>
