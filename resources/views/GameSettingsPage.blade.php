<?php
/* @var App\Models\Game $game */

/* @var App\Models\Player $player */
?>

<x-app-layout>
    <div class="max-w-2xl mx-auto mt-4 sm:px-6 lg:px-8 ">

        <div class="rounded-xl bg-purple-700 flex flex-wrap my-4 p-1">
            <div class="text-white w-full p-2 font-semibold text-center">
                <h1 class="text-xl">Game Setting</h1>
            </div>
            <div class="bg-purple-700 text-white rounded-xl w-full md:w-1/4 p-2 my-1">
                📊 Stats
            </div>
            <p class="text-white text-right p-2 my-1 w-full md:w-3/4">
                {{ $game->rounds->count() }}<b> Rounds</b>
                <br>
                {{ $game->moves()->where('user_id', \Illuminate\Support\Facades\Auth::id())->where('score', '>', 0)->count() }}
                <b> Rounds with success</b>
                <br>
                {{ $game->started_at->toDateString() }}<b> Started</b>
                <br>
                {{ $game->hostPlayer->user->name }}<b> is Host</b>
            </p>
            <div class="bg-purple-700 text-white rounded-xl w-full md:w-1/4 p-2 my-1">
                👋 Leave Game
            </div>
            <form class="p-2 my-1 w-full md:w-3/4 flex justify-end" method="DELETE" action="{{ route('player.destroy', ['player' => $game->authenticatedPlayer->uuid]) }}">
                @csrf
                <button type="submit" class="text-center rounded-xl h-7 w-36 mr-2 bg-white text-purple-700
                    hover:bg-purple-500 hover:text-white">
                    Leave Game
                </button>
            </form>
            <div class="bg-purple-700 text-white rounded-xl w-full md:w-1/4 p-2 my-1">
                🦶️ Kick Player
            </div>
            <div class="p-2 my-1 w-full md:w-3/4 flex justify-end">
                @foreach($game->players as $player)
                    <form method="DELETE" action="{{ route('player.destroy', ['player' => $player->uuid]) }}">
                        @csrf
                        <button type="submit" class="text-center text-purple-700 rounded-xl h-7 w-36 m-1 {{ 'bg-' . $player->passiveColor ?? 'pink-500' }} {{ 'hover:bg-' . $player->activeColor ?? 'pink-500' }}">
                            {{ $player->user->name }}
                        </button>
                    </form>
                @endforeach
            </div>
            @if($game->logic_identifier === \App\Models\WaveLengthGame::$logic_identifier)
                <div class="bg-purple-700 text-white rounded-xl w-full md:w-1/4 p-2 my-1">
                    ⚠️ End Round
                </div>
                <form class="p-2 my-1 w-full md:w-3/4 flex justify-end" method="DELETE" action="{{ route('wavelength.round', ['game' => $game->uuid]) }}">
                    @csrf
                    <button type="submit" class="text-center rounded-xl h-7 w-36 mr-2
                         bg-white text-purple-700 hover:bg-purple-500 hover:text-white">
                        Next Round
                    </button>
                </form>
            @endif
            <div class="bg-purple-700 text-white rounded-xl w-full md:w-1/4 p-2 my-1">
                🗑 End Game
            </div>
            <form class="p-2 my-1 w-full md:w-3/4 flex justify-end" method="DELETE" action="{{ route('game.destroy', ['game' => $game->uuid]) }}">
                @csrf
                <button type="submit" class="text-center rounded-xl h-7 w-36 mr-2 bg-red-700 text-white hover:bg-purple-500">
                    DESTROY
                </button>
            </form>
        </div>

        <div class="rounded-xl bg-pink-600 flex flex-wrap my-4 p-1">
            <div class="text-white w-full p-2 font-semibold text-center">
                <h1 class="text-xl">Player Setting</h1>
            </div>
            <div class="bg-pink-600 text-white rounded-xl w-full md:w-1/4 p-2 my-1">
                🎨 Color
            </div>
            <div class="bg-pink-400 rounded-xl flex-grow p-2 my-1 w-full md:w-3/4">
                <form method="PUT" action="{{ route('player.update', ['player' => $player->uuid]) }}">
                    <select name="color" id="color" onchange="this.form.submit()" class="px-4 rounded-xl ">
                        <option value="gray" class="bg-gray-500">gray</option>
                        <option value="red" class="bg-red-500">red</option>
                        <option value="orange" class="bg-orange-500">orange</option>
                        <option value="yellow" class="bg-yellow-500">yellow</option>
                        <option value="green" class="bg-green-500">green</option>
                        <option value="teal" class="bg-teal-500">teal</option>
                        <option value="cyan" class="bg-cyan-500">cyan</option>
                        <option value="blue" class="bg-blue-500">blue</option>
                        <option value="purple" class="bg-purple-500">purple</option>
                        <option value="pink" class="bg-pink-500">pink</option>
                    </select>
                </form>
            </div>
            <div class="bg-pink-600 text-white rounded-xl w-full md:w-1/4 p-2 my-1">
                🥸️ Change Name
            </div>
            <div class="bg-pink-400 rounded-xl flex-grow p-2 my-1 w-full md:w-3/4 flex flex-wrap">
                <form method="PUT" class="w-full flex justify-between" action="{{ route('player.update', ['player' => $player->uuid]) }}">
                    @csrf

                    <div class="flex flex-col text-left mb-2">
                        <input id="name"
                            class="border-b-2 border-white bg-transparent"
                            name="name"
                        />
                        <label for="name" class="text-white text-sm">Name</label>
                        @error('name')<p class="input-error">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <button type="submit"
                            class="bg-pink-700 text-white rounded-full hover:bg-purple-500 py-2 px-4">
                            Rename
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="rounded-xl bg-orange-500 flex flex-wrap my-4 p-1">
            <div class="bg-orange-500 text-white rounded-xl w-full md:w-1/4 p-2 my-1">
                ✉️️ Change Email
            </div>
            <div class="bg-orange-200 rounded-xl flex-grow p-2 my-1 w-full md:w-3/4 flex flex-wrap">
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
            <div class="bg-orange-500 text-white rounded-xl w-full md:w-1/4 p-2 my-1">
                🔐 Change Password
            </div>
            <div class="bg-orange-200 rounded-xl flex-grow p-2 my-1 w-full md:w-3/4 flex flex-wrap">
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
            <div class="bg-orange-500 text-white rounded-xl w-full md:w-1/4 p-2 my-1">
                🚨 Delete Account
            </div>
            <div class="bg-orange-200 rounded-xl flex-grow p-2 my-1 w-full md:w-3/4 flex flex-wrap">
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
