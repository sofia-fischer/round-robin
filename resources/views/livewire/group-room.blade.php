<?php
/* @var App\Models\Group $group */
?>

<div class="max-w-7xl mt-4 mx-auto sm:px-6 lg:px-8 flex flex-wrap content-center justify-between">

    {{--  Token  --}}
    <div class="bg-white shadow-xl m-1 w-full">
        <div class="p-4 bg-gray-700">
            <h2 class="text-white text-lg font-semibold mb-4 text-center">
                Group Token: <label class="text-pink-500">{{ $group->token }}</label>
            </h2>
        </div>
    </div>

    <div class="w-full">
        <livewire:player-overview-component :group="$group"></livewire:player-overview-component>
    </div>

    {{--  Auth Settings  --}}
    <div class="bg-white shadow-xl m-1 flex-grow">
        <div class="p-4 bg-gray-700">
            <h2 class="text-white text-lg font-semibold mb-4 text-center">
                Settings
            </h2>
        </div>

        {{--  Game Settings  --}}
        <div class="p-4">
            @if($gameId || $group->host_user_id == Auth::id())
                <div class="flex mb-8 mx-4">
                    <button
                        class="bg-pink-500 text-white rounded-full px-4 py-2 w-64 mx-auto"
                        wire:click="joinGame">
                        {{ $gameId ? 'Join ' : ' Start ' }}
                    </button>
                </div>
            @endif

            <div class="flex my-8 mx-4">
                <label class="self-center mr-4">Name</label>
                <input wire:model.defer='playerName'
                       class="flex-grow border-b-2 border-{{ $group->authenticatedPlayer->color }}-500 "
                       wire:keydown.enter="updatePlayerName">
            </div>

            <div class="flex my-8 mx-4">
                <label for='color' class="self-center mr-4">Color</label>
                <select id="color" class="flex-grow border-b-2 border-{{ $group->authenticatedPlayer->color }}-500"
                        wire:change="updateColor"
                        wire:model="color">
                    <option value="orange">Orange</option>
                    <option value="red">Red</option>
                    <option value="yellow">Yellow</option>
                    <option value="green">Green</option>
                    <option value="blue">Blue</option>
                    <option value="purple">Purple</option>
                    <option value="pink">Pink</option>
                    <option value="gray">Gray</option>
                </select>
            </div>
        </div>
    </div>

    {{--  Games  --}}
    <div class="bg-white shadow-xl m-1 flex-grow">
        <div class="p-4 bg-gray-700">
            <h2 class="text-white text-lg font-semibold mb-4 text-center">
                Join a game
            </h2>
        </div>

        <div class="flex flex-col content-center">
            @foreach($group->games as $game)
                <button class="p-4 font-semibold w-full
                        {{ $game->id == $gameId ? 'bg-gray-200 text-pink-500' : 'hover:bg-gray-100 text-gray-500 ' }}"
                        wire:click="$set('gameId', {{ $game->id }})">
                    {{ $game->logic->name }}
                    <label class="text-gray-500">
                        (Round {{ $game->rounds()->count() }})
                    </label>
                </button>
            @endforeach
        </div>

        <div class="p-4 bg-gray-700">
            <h2 class="text-white text-lg font-semibold mb-4 text-center">
                Start a new Game
            </h2>
        </div>

        <div class="flex flex-col content-center">
            @foreach(\App\Models\GameLogic::all() as $gameLogic)
                <button class="p-4 hover:bg-pink-100 text-gray-500 font-semibold"
                        wire:click="startNewGame({{ $gameLogic->id }})">
                    {{ $gameLogic->name }}
                </button>
            @endforeach
        </div>
    </div>
</div>
