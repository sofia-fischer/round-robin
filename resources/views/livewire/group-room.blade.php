<?php
/* @var App\Models\Group $group */
?>

<div class="max-w-7xl mt-4 mx-auto bg-white">

    <div class="w-full">
        <livewire:player-overview-component :group="$group"></livewire:player-overview-component>
    </div>

    {{--  Games  --}}
    <div class="max-w-2xl mx-auto">
        <p class="text-gray-500 p-4 text-center">
            Join an active game. The Host of the round may start new games.
        </p>

        <div class="flex flex-wrap justify-evenly p-4">
            @foreach($group->games as $game)
                <button class="py-2 px-4 m-2 font-semibold rounded-full border border-gray-700 text-gray-700 hover:bg-gray-200"
                    wire:click="joinGame({{ $game->id }})">
                    {{ $game->logic->name }}
                    <label class="text-gray-400 text-sm">
                        (Round {{ $game->rounds()->count() }})
                    </label>
                </button>
            @endforeach
        </div>

        <p class="text-gray-500 p-4 text-center">
            As Host you may start a new Game.
        </p>

        <div class="flex flex-wrap justify-evenly p-4">
            @foreach(\App\Models\GameLogic::all() as $gameLogic)
                <button class="py-2 px-4 m-2 font-semibold rounded-full border border-green-700 text-green-700 hover:bg-green-300"
                        wire:click="startNewGame({{ $gameLogic->id }})">
                    {{ $gameLogic->name }}
                </button>
            @endforeach
        </div>
    </div>
</div>
